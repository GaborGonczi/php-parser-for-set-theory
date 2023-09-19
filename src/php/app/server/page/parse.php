<?php
use core\parser\exception\ParserException;

require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/autoloader.php';

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/rootfolder.php';
//require_once dirname(dirname(dirname(dirname(__FILE__)))).'/core/parser/Lexer.php';
//require_once dirname(dirname(dirname(dirname(__FILE__)))).'/core/parser/Parser.php';
require_once dirname(dirname(__FILE__)) . '/db.php';

use \app\server\classes\model\File;
use \app\server\classes\model\Expression;
use \app\server\classes\model\Log;
use \app\server\classes\model\User;

use \core\parser\Lexer;
use \core\parser\Parser;
use \core\parser\exception\LexerException;
use \core\lib\Map;

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
    }, $array);
}

header('Content-Type: application/json');
session_start();
if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])) {
    $location = rootfolder() . '/index.php';
    header("Location:$location");
    exit(1);
} else {
    $user = unserialize($_SESSION[$_COOKIE['PHPSESSID']]['authedUser']);
}
global $db;
$data = ['json' => [], 'variables' => new Map([])];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])) {
        $file = new File(null, $user->getId(), date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()), date('Y-m-d H:i:s', (new \DateTime('now'))->getTimestamp()), null);
        $id = $db->insert('files', $file->getAsAssociativeArray());
        if ($id) {
            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'] = $id;
        }
    }
    $stmtdata = (array) json_decode(file_get_contents("php://input"));
    $file_id=intval($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']);
    $start=$stmtdata['start'];
    if ($stmtdata['noparse'] == true) {
        if ($db->isExist('expressions', ['file_id' =>$file_id , 'start' => $start])) {

            $expressionsdata = $db->get('expressions', ['file_id' => $file_id, 'start' => $start]);

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

        $lexer = new Lexer($stmtdata['statement']);
        try {
            $tokens = $lexer->tokenize();
        } catch (LexerException $le) {
            echo json_encode($le);
        }
        $parser = new Parser($tokens);
        try {
            $result = $parser->parse();
        } catch (ParserException $pe) {
            echo json_encode($pe);
        }

        $stmtdata['result'] = $result;
        $newvars = $parser->getVars();
        $data['variables'] = merge_maps($data['variables'], $newvars);

        if($db->isExist('expressions', ['file_id' =>$file_id, 'start' =>$start])){
            $expressionsdata = $db->get('expressions', ['file_id' => $file_id, 'start' => $start]);
            foreach ($expressionsdata as $expressiondata) {
                $expressiondata=(array)$expressiondata;
                $expression = new Expression(
                    $expressiondata['id'],
                    $expressiondata['file_id'],
                    html_entity_decode($stmtdata['statement']),
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
            $expression = new Expression(
                null,
                $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'],
                html_entity_decode($stmtdata['statement']),
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
       

    }
    $fileid = $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'];
    $file_content = $db->get('expressions', [
        'file_id' => $fileid
    ]);
    foreach ($file_content as $expression) {
        unset($expression['length']);
        $expressionModel = new Expression(...holdsNull((array) $expression));
        if (strpos($expressionModel->getResult(), 'data:image/png;base64,') !== false) {
            $image = $expressionModel->getResult();
            $expressionModel->setResult("");
            $data['json'] = array_merge($expressionModel->getAsAssociativeArray(), ["diagram" => $image]);
        } else {
            $data['json'][] = $expressionModel->getAsAssociativeArray();
        }


    }

    echo json_encode($data);


} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])) {
        echo json_encode($data);
    } else {
        $fileid = $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'];
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
                    $data['json'] = array_merge($expressionModel->getAsAssociativeArray(), ["diagram" => $image]);
                } else {
                    $data['json'][] = $expressionModel->getAsAssociativeArray();
                }
            }
        }
        echo json_encode($data);
    }
}