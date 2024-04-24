<?php
namespace app\server\classes\runnable;


use \core\lib\datastructures\Set;
use \core\lib\datastructures\Point;
use \core\lib\datastructures\Map;

use \app\server\classes\model\User;
use \app\server\classes\model\File;
use \app\server\classes\model\Expression;
use \app\server\classes\model\Log;
use \app\server\classes\model\Variable;
use \app\server\classes\model\Automaton;

use \core\parser\Lexer;
use \core\parser\Parser;

use \core\HtmlEntityTable;

use \app\server\classes\Database;

use utils\Lang;
use \utils\Rootfolder;

use \DateTime;

class ParseScript extends Runnable
{

    private Lexer $lexer;

    private Parser $parser;

    private array $data;

    public function __construct(User $authedUser,Database $db,string $lang='hun')  {
        parent::__construct($authedUser,$db,$lang);
        $this->lexer=new Lexer('',false,$this->lang);
        $this->parser=new Parser([],false,$this->lang);
        $this->data=['json' => [], 'variables' => new Map([])];
    }

    public function run():string {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])) {
                $this->createNewFile();
            }
            $stmtdata = json_decode(file_get_contents("php://input"),true);
            $fileid = intval($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']);
            $expressionId = $stmtdata['id'];
            if ($stmtdata['noparse'] == true) {
                if ($this->isExpressionExist($stmtdata['id'], $fileid)) {
                    $expressionsdata = $this->getExpressionById($fileid, $expressionId);
                    foreach ($expressionsdata as $expressiondata) {
                        $expressiondata = (array) $expressiondata;
                        $expression = new Expression(
                            $expressiondata['id'],
                            $expressiondata['file_id'],
                            htmlentities($stmtdata['statement']),
                            htmlentities($stmtdata['statement']),
                            $stmtdata['start'],
                            $stmtdata['end'],
                            $stmtdata['noparse'],
                            $stmtdata['row'],
                            $expressiondata['created_at'],
                            date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                            null
                        );
                        if ($this->isExpressionUpdated($expression->getAsAssociativeArray())) {
                            $this->isLogsCreated($stmtdata,$expression->getId());
                        }
                    }
        
                } else {
                    $expression = new Expression(
                        null,
                        $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'],
                        htmlentities($stmtdata['statement']),
                        htmlentities($stmtdata['statement']),
                        $stmtdata['start'],
                        $stmtdata['end'],
                        $stmtdata['noparse'],
                        $stmtdata['row'],
                        date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                        date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                        null
                    );
        
                    if ($id = $this->isExpressionCreated($expression->getAsAssociativeArray())) {
                        $this->isLogsCreated($stmtdata,$id);
                    }
                }
        
            } else {
                if($stmtdata['derrorMessages']==true){
                    $this->lexer->setDevErrorMessages(true);
                    $this->parser->setDevErrorMessages(true);
                }
                $variables = $this->getVariables($fileid);
                $vars = $this->createVariableMap($variables);
                $this->parser->setVars($vars);
                $this->data['variables'] = $vars;
        
                $this->lexer->setInput($stmtdata['statement']);
                $tokens = $this->lexer->getTokens();
               
                if (gettype($tokens) === "string") {
                    $result = $tokens;
                } else {
                    $this->parser->setTokens($tokens);
                    if($stmtdata['gdfa']==true){
                        $this->parser->initDFADiagramBuilder($this->user);
                    }
                    $result = $this->parser->parse();
                }
        
                $stmtdata['result'] = $result;
                $newvars = $this->parser->getVars();
                $this->data['variables'] = $newvars;
        
                if ($this->isExpressionExist($stmtdata['id'], $fileid)) {
                    $expressionsdata =$this->getExpressionById($fileid, $expressionId);
                    foreach ($expressionsdata as $expressiondata) {
                        $expressiondata = (array) $expressiondata;
                        $stmtdata['statement'] = $this->replaceHTMLEntities($stmtdata['statement']);
                        if($stmtdata['gdfa']==true&&gettype($tokens) !== "string"){
                            $filename=$expressiondata['file_id'].'_'.$expressiondata['id'];
                            $this->parser->getDFADiagramBuilder()->generateOutput($filename);
                            $url=$this->parser->getDFADiagramBuilder()->getOutputUrl($filename);
                            $automaton=new Automaton(null,
                                                    $expressiondata['id'],
                                                    $url,
                                                    date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                                                    date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                                                    null);
                            $this->isAutomatonUpdated($automaton->getAsAssociativeArray());

                        }
                        if ($this->isResulTypeFile($stmtdata['result'])) {
                            $stmtdata['result'] = $this->renderFileResult($fileid, $expressiondata);
                        }
                        $expression = new Expression(
                            $expressiondata['id'],
                            $expressiondata['file_id'],
                            htmlentities($stmtdata['statement']),
                            htmlentities($stmtdata['result']),
                            $stmtdata['start'],
                            $stmtdata['end'],
                            $stmtdata['noparse'],
                            $stmtdata['row'],
                            $expressiondata['created_at'],
                            date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                            null
                        );
                        if ($this->isExpressionUpdated($expression->getAsAssociativeArray())) {
                            $this->isLogsCreated($stmtdata,$expression->getId());
                        }
                    }
        
                } else {
                    $stmtdata['statement'] = $this->replaceHTMLEntities($stmtdata['statement']);
                    $expression = new Expression(
                        null,
                        $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'],
                        htmlentities($stmtdata['statement']),
                        htmlentities($stmtdata['result']),
                        $stmtdata['start'],
                        $stmtdata['end'],
                        $stmtdata['noparse'],
                        $stmtdata['row'],
                        date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                        date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                        null,
                    );
                    if ($id = $this->isExpressionCreated($expression->getAsAssociativeArray())) {
                        $this->isLogsCreated($stmtdata,$id);
                    }
                    if($stmtdata['gdfa']==true&&gettype($tokens) !== "string"){
                        $filename=$fileid.'_'.$id;
                        $this->parser->getDFADiagramBuilder()->generateOutput($filename);
                        $url=$this->parser->getDFADiagramBuilder()->getOutputUrl($filename);
                        $automaton=new Automaton(null,
                                                $id,
                                                $url,
                                                date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                                                date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                                                null);
                        $id = $this->isAutomatonCreated($automaton->getAsAssociativeArray());

                    }
                    if ($this->isResulTypeFile($stmtdata['result'])) {
                        $expressions = $this->getExpressionById($fileid, $id);
                        foreach ($expressions as $expressiondata) {
                            $expressiondata = (array) $expressiondata;
                            $expression = new Expression(
                                $expressiondata['id'],
                                $expressiondata['file_id'],
                                htmlentities($expressiondata['statement']),
                                htmlentities($expressiondata['result']),
                                $expressiondata['start'],
                                $expressiondata['end'],
                                $expressiondata['noparse'],
                                $expressiondata['row'],
                                $expressiondata['created_at'],
                                $expressiondata['modified_at'],
                                null
                            );
                        }
        
                        $expression->setResult($this->renderFileResult($fileid, $expression->getAsAssociativeArray()));
                        $rowcount =$this->isExpressionUpdated($expression->getAsAssociativeArray()); 
                    }
        
                }
        
                foreach ($newvars as $name => $value) {
                    if ($this->isVariableExist($fileid, $name)) {
                        $variabledata = $this->getVariableByName($fileid, $name);
                        foreach ($variabledata as $variabledata) {
                            $variabledata = (array) $variabledata;
                            $variable = new Variable(
                                $variabledata['id'],
                                $variabledata['file_id'],
                                $name,
                                json_encode($value),
                                $variabledata['created_at'],
                                date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                                null
                            );
                            $rowcount =$this->isVariableUpdated($variable->getAsAssociativeArray());
                        }
                    } else {
                        $variable = new Variable(
                            null,
                            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'],
                            $name,
                            json_encode($value),
                            date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                            date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()),
                            null
                        );
                        $id = $this->isVariableCreated($variable);
                    }
                }
            
            }
            $file_content = $this->getFileContent($fileid);
        
            $this->processFileContent($file_content);

            if($this->baseSetNeedToBeDefined($file_content)&&!$this->isBaseSetDefined($this->getVariables($fileid))){
                $this->data['baseSet']=false;
            }
            else{
                $this->data['baseSet']=true;
            }
            $variables =$this->getVariables($fileid);
            $vars = $this->createVariableMap($variables);
            $this->parser->setVars($vars);
            $this->data['variables'] = $vars;       
            return (string)json_encode($this->data);
        
        
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
            if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])) {
               $this->createNewFile();
               $fileid = intval($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']);
               $file_content = $this->getFileContent($fileid);
               if($this->baseSetNeedToBeDefined($file_content)&&!$this->isBaseSetDefined($this->getVariables($fileid))){
                    $this->data['baseSet']=false;
                }
                else{
                    $this->data['baseSet']=true;
                } 
               return (string)json_encode($this->data);
            } else {
                $fileid = intval($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']);
                $file_content = $this->getFileContent($fileid);
                $this->processFileContent($file_content);
                if($this->baseSetNeedToBeDefined($file_content)&&!$this->isBaseSetDefined($this->getVariables($fileid))){
                    $this->data['baseSet']=false;
                }
                else{
                    $this->data['baseSet']=true;
                } 
                $variables =$this->getVariables($fileid);
                $vars = $this->createVariableMap($variables);
                $this->parser->setVars($vars);
                $this->data['variables'] = $vars;
        
                return (string)json_encode($this->data);
            }
        }
        return "";
    }

    private function holdsNull($array)
    {
        return array_map(function ($value) {
            if ($value === null) {
                return 'null';
            }
            return $value;
        }, $array);
    }

    private function getApropriateObject($array)
    {
        if ($array['name'] === "Set") {
            if (!in_array($array['type'], array('integer', 'double', 'boolean'))) {
                $objects = [];
                foreach ($array['elements'] as $value) {
                    if ($array['type'] === 'core\lib\datastructures\Point') {
                        $p = new Point($value['x'], $value['y']);
                        $objects[] = $p;
                    }
                }
                $array['elements'] = $objects;
            }
            return new Set([...$array['elements']]);
        } else if ($array['name'] === "Point") {
            return new Point($array['x'], $array['y']);
        }

    }

    private function createNewFile() 
    {
        $file = new File(null, $this->user->getId(),false, date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), null);
        $id = $this->db->insert('files', $file->getAsAssociativeArray());
        if ($id) {
            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'] = $id;
        }
    }
    private function getFileContent($fileid)
    {
        return $this->db->get('expressions', [
            'file_id' => $fileid,
            'deleted_at' => null
        ]) ?: [];
    }
    private function processFileContent($file_content)
    {
        foreach ($file_content as $expression) {
            unset($expression['length']);
            $expressionModel = new Expression(...$this->holdsNull((array) $expression));
            $expressionModel->setStatement(html_entity_decode($expressionModel->getStatement()));
            $expressionModel->setResult(html_entity_decode($expressionModel->getResult()));
            if ($this->isResulTypeFile($expressionModel->getResult())) {
                $this->data['json'][] = array_merge($expressionModel->getAsAssociativeArray(), ["diagram" => true]);
            } else {
                $this->data['json'][] = array_merge($expressionModel->getAsAssociativeArray(), []);
            }
        }
    }
    private function isExpressionExist($expressionId, $fileid)
    {
        return !$expressionId == null && $this->db->isExist('expressions', ['file_id' => $fileid, 'id' => $expressionId, 'deleted_at' => null]);
    }
    private function getExpressionById( $fileid, $expressionId)
    {
        return $this->db->get('expressions', ['file_id' => $fileid, 'id' => $expressionId, 'deleted_at' => null]) ?: [];
    }
    private function isExpressionUpdated( $expressionAsArray)
    {
        unset($expressionAsArray['length']);
        return $this->db->update('expressions', $expressionAsArray, ['id' => $expressionAsArray['id']]) !== false;
    }
    private function isExpressionCreated($expressionArray)
    {
        unset($expressionArray['length']);
        return $this->db->insert('expressions', $expressionArray);
    }

    private function isAutomatonUpdated( $automatonAsArray)
    {
        return $this->db->update('automatons', $automatonAsArray, ['id' => $automatonAsArray['id']]) !== false;
    }
    private function isAutomatonCreated($automatonAsArray)
    {
        return $this->db->insert('automatons', $automatonAsArray);
    }

    private function isVariableExist( $fileid, $name)
    {
        return $this->db->isExist('variables', ['file_id' => $fileid, 'name' => $name, 'deleted_at' => null]);
    }
    private function getVariables( $fileid)
    {
        return $this->db->get('variables', [
            'file_id' => $fileid,
            'deleted_at' => null
        ]) ?: [];
    }
    private function getVariableByName( $fileid, $name)
    {
        return $this->db->get('variables', ['file_id' => $fileid, 'name' => $name, 'deleted_at' => null]) ?: [];
    }
    private function isVariableUpdated( $variableAsArray)
    {
        return $this->db->update('variables', $variableAsArray, ['id' => $variableAsArray['id']]) !== false;
    }
    private function isVariableCreated($variable)
    {
        return $this->db->insert('variables', $variable->getAsAssociativeArray());
    }

    private function isLogsCreated($stmtdata,$expressionId){
        foreach ($stmtdata['beforelogs'] as $log) {
            $logObj = new Log(...array_merge(['id' => null, 'expression_id' => $expressionId], $this->holdsNull((array) $log), ['created_at' => date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp())]));
            $this->db->insert('logs', $logObj->getAsAssociativeArray());
        }
    }

    private function createVariableMap($variables)
    {
        $varMap = new Map([]);
        foreach ($variables as $variable) {
            $variableModel = new Variable(...$this->holdsNull((array) $variable));
            $name = $variableModel->getName();
            $value = $this->getApropriateObject(json_decode($variableModel->getValue(), true));
            $varMap->add($name, $value);
        }
        return $varMap;
    }
    private function replaceHTMLEntities($statement)
    {
        foreach (HtmlEntityTable::TABLE as $key => $value) {
            $statement = str_replace($key, $value, $statement);
        }
        return $statement;
    }
    private function isResulTypeFile($result)
    {
        return strpos($result, '.html') !== false;
    }

    private function renderFileResult($fileid, $expressiondata)
    {
        rename(Rootfolder::getPhysicalPath().'/images/image.html', Rootfolder::getPhysicalPath().'/images/image_' . $fileid . '_' . $expressiondata['id'] . '.html');
        return getenv('BASEURL').'/images/image_' . $fileid . '_' . $expressiondata['id'] . '.html';
    }

    private function isBaseSetDefined($file_vars){
        $baseSetarray=array_filter($file_vars,function ($var) {
            return $var['name']==='H';
        });
        return !empty($baseSetarray);
    }
    private function baseSetNeedToBeDefined($file_content){
        foreach ($file_content as  $expression) {
            unset($expression['length']);
            $expressionModel = new Expression(...$this->holdsNull((array) $expression));
            if($expressionModel->getResult()===$this->baseSetNotDefinedError()){
                return true;
            }
        }
        return false;
    }

    private function baseSetNotDefinedError(){
        return htmlentities(Lang::getString('baseSetNotDefinedError',$this->lang));
    }
}
