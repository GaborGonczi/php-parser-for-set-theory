<?php

require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/autoloader.php';


require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/core/parser/Lexer.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/core/parser/Parser.php';
require_once dirname(dirname(__FILE__)).'/db.php';

use \app\server\classes\model\File;
use \app\server\classes\model\Expression;
use \app\server\classes\model\Log;
use \app\server\classes\model\User;
use \app\server\classes\model\Variable;

use \core\lib\datastructures\Set;
use \core\lib\datastructures\Point;

use \core\parser\Lexer;
use \core\parser\Parser;
use \core\parser\exception\LexerException;
use \core\parser\exception\ParserException;
use \core\parser\exception\SemanticException;
use \core\parser\exception\UndefinedVariableException;
use \core\lib\datastructures\Map;
use \core\HtmlEntityTable;

function merge_maps(Map &$map1, Map &$map2)
{
    foreach ($map2 as $key => $value) {
        $map1->add($key, $value);
    }
    return $map1;
}

function holdsNull($array)
{
    return array_map(function ($value) {
        if ($value === null) {
            return 'null';
        }
        return $value;
    },$array);
}

function getApropriateObject($array){
    if($array['name']==="Set"){
        return new Set([...$array['elements']]);
    }
    else if($array['name']==="Point"){
        return new Point($array['x'],$array['y']);
    }
   
}

header('Content-Type: application/json');
session_start();
if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $location=rootfolder().'/index.php';
    header("Location:$location");
    exit(1);
}
else{
    $user=unserialize($_SESSION[$_COOKIE['PHPSESSID']]['authedUser']);
}
global $db;

$lexer=new Lexer();
$parser=new Parser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data=['json'=>[],'variables'=>new Map([])];
    if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])) {
        $file = new File(null, $user->getId(), date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()), date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()), null);
        $id = $db->insert('files', $file->getAsAssociativeArray());
        if ($id) {
            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'] = $id;
        }
    }
    $stmtdata = (array) json_decode(file_get_contents("php://input"));
    $fileid=intval($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']);
    $start=$stmtdata['start'];
    if ($stmtdata['noparse'] == true) {
        if ($db->isExist('expressions', ['file_id' =>$fileid , 'start' => $start])) {

            $expressionsdata = $db->get('expressions', ['file_id' => $fileid, 'start' => $start]);

            foreach ($expressionsdata as $expressiondata) {
                $expressiondata=(array)$expressiondata;
                $expression = new Expression(
                    $expressiondata['id'],
                    $expressiondata['file_id'],
                    $stmtdata['statement'],
                    $stmtdata['statement'],
                    $stmtdata['start'],
                    $stmtdata['end'],
                    $stmtdata['noparse'],
                    $expressiondata['created_at'],
                    date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()),
                    null
                );
                $expressionAsArray=$expression->getAsAssociativeArray();
                $rowcount = $db->update('expressions',$expressionAsArray , ['id' => $expressionAsArray['id']]);
                if ($rowcount) {
                    foreach ($stmtdata['beforelogs'] as $log) {
                        $logObj = new Log(...array_merge(['id' => null, 'expression_id' => $expressiondata['id']], holdsNull((array) $log), ['created_at' => date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp())]));
                        $db->insert('logs', $logObj->getAsAssociativeArray());
                    }
                }
            }

            
        } else {
            $expression = new Expression(
                null,
                $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'],
                $stmtdata['statement'],
                $stmtdata['statement'],
                $stmtdata['start'],
                $stmtdata['end'],
                $stmtdata['noparse'],
                date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()),
                date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()),
                null
            );
            $id = $db->insert('expressions', $expression->getAsAssociativeArray());
            if ($id) {
                foreach ($stmtdata['beforelogs'] as $log) {
                    $logObj = new Log(...array_merge(['id' => null, 'expression_id' => $id], holdsNull((array) $log), ['created_at' => date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp())]));
                    $db->insert('logs', $logObj->getAsAssociativeArray());
                }
            }
        }

    } else {

        $variables=$db->get('variables', [
            'file_id' => $fileid
        ]);
        if($variables){
            $vars=new Map([]);
            foreach ($variables as $variable) {
                $variableModel = new Variable(...holdsNull((array) $variable));
                $name=$variableModel->getName();
                $value=getApropriateObject(json_decode($variableModel->getValue(),true));
                $vars->add($name,$value); 
            }
            $parser->setVars($vars);
            $data['variables']=$vars;
            
            
        }

        $lexer->setInput($stmtdata['statement']);
        try {
            $tokens = $lexer->tokenize();
        } catch (LexerException $le) {
            echo json_encode($le);
        }
        $parser->setTokens($tokens);
        try {
            $result = $parser->parse();
        } catch (SemanticException $se) {
            echo json_encode($se);
        } catch (UndefinedVariableException $uve) {
            echo json_encode($uve);
        } catch (ParserException $pe) {
            echo json_encode($pe);
        } catch (InvalidArgumentException $ie){
            echo json_encode($ie);
        }

        $stmtdata['result'] = $result;
        $newvars = $parser->getVars();
        $data['variables'] = $newvars;

        if($db->isExist('expressions', ['file_id' =>$fileid, 'start' =>$start])){
            $expressionsdata = $db->get('expressions', ['file_id' => $fileid, 'start' => $start]);
            foreach ($expressionsdata as $expressiondata) {
                $expressiondata=(array)$expressiondata;
                foreach (HtmlEntityTable::TABLE as $key => $value) {
                    $stmtdata['statement']= str_replace($key,$value,$stmtdata['statement']);
                }
                $expression = new Expression(
                    $expressiondata['id'],
                    $expressiondata['file_id'],
                    $stmtdata['statement'],
                    $stmtdata['result'],
                    $stmtdata['start'],
                    $stmtdata['end'],
                    $stmtdata['noparse'],
                    $expressiondata['created_at'],
                    date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()),
                    null
                );
                $expressionAsArray=$expression->getAsAssociativeArray();
                $rowcount = $db->update('expressions', $expressionAsArray, ['id' => $expressionAsArray['id']]);
                if ($rowcount) {
                    foreach ($stmtdata['beforelogs'] as $log) {
                        $logObj = new Log(...array_merge(['id' => null, 'expression_id' => $expressiondata['id']], holdsNull((array) $log), ['created_at' => date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp())]));
                        $db->insert('logs', $logObj->getAsAssociativeArray());
                    }
                }
            }
            
        }
        else{
            foreach (HtmlEntityTable::TABLE as $key => $value) {
                $stmtdata['statement']= str_replace($key,$value,$stmtdata['statement']);
            }
            $expression = new Expression(
                null,
                $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'],
                $stmtdata['statement'],
                $stmtdata['result'],
                $stmtdata['start'],
                $stmtdata['end'],
                $stmtdata['noparse'],
                date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()),
                date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()),
                null,
            );
            $id = $db->insert('expressions', $expression->getAsAssociativeArray());
            if ($id) {
                foreach ($stmtdata['beforelogs'] as $log) {
                    $logObj = new Log(...array_merge(['id' => null, 'expression_id' => $id], holdsNull((array) $log), ['created_at' => date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp())]));
                    $db->insert('logs', $logObj->getAsAssociativeArray());
                }
            }
        }

        foreach ($newvars as $name=>$value) {
            if($db->isExist('variables', ['file_id' =>$fileid, 'name' =>$name])){
                $variabledata = $db->get('variables', ['file_id' => $fileid, 'name' => $name]);
                foreach ($variabledata as $variabledata) {
                    $variabledata=(array)$variabledata;
                    $variable = new Variable(
                        $variabledata['id'],
                        $variabledata['file_id'],
                        $name,
                        json_encode($value),                 
                        $variabledata['created_at'],
                        date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()),
                        null
                    );
                    $variableAsArray=$variable->getAsAssociativeArray();
                    $rowcount = $db->update('variables', $variableAsArray, ['id' => $variableAsArray['id']]);
                }
            }
            else{
                $variable = new Variable(
                    null,
                    $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'],
                    $name,
                    json_encode($value),                 
                    date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()),
                    date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()),
                    null
                );
                $id = $db->insert('variables', $variable->getAsAssociativeArray());
            }
        }

    }
    $file_content = $db->get('expressions', [
        'file_id' => $fileid
    ]);
    foreach ($file_content as $expression) {
        unset($expression['length']);
        $expressionModel=new Expression(...holdsNull((array)$expression));
        if(strpos($expressionModel->getResult(),'data:image/png;base64,')!==false){
            $image=$expressionModel->getResult();
            $expressionModel->setResult("");
            $data['json'][] = array_merge($expressionModel->getAsAssociativeArray(), ["diagram" => $image]);
        } else {
            $data['json'][] = $expressionModel->getAsAssociativeArray();
        }
        

    }

    echo json_encode($data);
    
    
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = ['json' => [], 'variables' => new Map([])];
    if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])) {
        echo json_encode($data);
    } else {
        $fileid=intval($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']);
        $file_content = $db->get('expressions', [
            'file_id' => $fileid
        ]);
        if ($file_content) {
            foreach ($file_content as $expression) {
                unset($expression['length']);
                $expressionModel = new Expression(...holdsNull((array) $expression));
                if (strpos($expressionModel->getResult(), 'data:image/png;base64,') !== false) {
                    $image = $expressionModel->getResult();
                    $expressionModel->setResult("");
                    $data['json'][] = array_merge($expressionModel->getAsAssociativeArray(), ["diagram" => $image]);
                } else {
                    $data['json'][] = $expressionModel->getAsAssociativeArray();
                }
            }
        }
        $variables=$db->get('variables', [
            'file_id' => $fileid
        ]);
        if($variables){
            $vars=new Map([]);
            foreach ($variables as $variable) {
                $variableModel = new Variable(...holdsNull((array) $variable));
                $name=$variableModel->getName();
                $value=getApropriateObject(json_decode($variableModel->getValue(),true));
                $vars->add($name,$value);       
            }
            $parser->setVars($vars);
            $data['variables']=$vars;
        }
       
        echo json_encode($data);
    }
}
