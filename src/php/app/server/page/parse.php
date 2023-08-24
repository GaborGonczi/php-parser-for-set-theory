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

function holdsNull($array){
    return array_map(function($value){
        if($value===null){
            return 'null';
        }
        return $value;
    },$array);
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
$data=['json'=>[]];

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])){
        $file=new File(null,$user->getId(),date('Y-m-d H:i:s',(new \DateTime('now'))->getTimestamp()),date('Y-m-d H:i:s',(new \DateTime('now'))->getTimestamp()),null);
        $id=$db->insert('files',$file->getAsAssociativeArray());
        if($id){
            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']=$id;
        }
    }
    $stmtdata=(array)json_decode(file_get_contents("php://input"));

    if($stmtdata['noparse']==true){
        $expression=new Expression(null,$_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'],$stmtdata['statement'],$stmtdata['statement'],$stmtdata['start'],$stmtdata['end'],
        date('Y-m-d H:i:s',(new \DateTime('now'))->getTimestamp()),date('Y-m-d H:i:s',(new \DateTime('now'))->getTimestamp()),null,null);
        $id=$db->insert('expressions',$expression->getAsAssociativeArray());
        if($id){
            foreach ($stmtdata['beforelogs'] as $log) {
                $logObj=new Log(...array_merge(['id'=>null,'expression_id'=>$id],holdsNull((array)$log),['created_at'=>date('Y-m-d H:i:s',(new \DateTime('now'))->getTimestamp())]));
                $db->insert('logs',$logObj->getAsAssociativeArray());
            }
        }
    }
    else{
        //TODO:
        //$lexer=new Lexer($stmtdata['statement']);
        //$parser=new Parser($lexer->tokenize());
        //$stmtdata['result']=$parser->parse();
        $expression=new Expression(null,$_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'],$stmtdata['statement'],$stmtdata['result'],$stmtdata['start'],$stmtdata['end'],
        date('Y-m-d H:i:s',(new \DateTime('now'))->getTimestamp()),date('Y-m-d H:i:s',(new \DateTime('now'))->getTimestamp()),null,null);
        $id=$db->insert('expressions',$expression->getAsAssociativeArray());
        if($id){
            foreach ($stmtdata['beforelogs'] as $log) {
                $logObj=new Log(...array_merge(['id'=>null,'expression_id'=>$id],holdsNull((array)$log),['created_at'=>date('Y-m-d H:i:s',(new \DateTime('now'))->getTimestamp())]));
                $db->insert('logs',$logObj->getAsAssociativeArray());
            }
        }
        
    }
    $fileid=$_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'];
    $file_content=$db->get('expressions',[
        'file_id'=>$fileid
    ]);
    foreach ($file_content as $expression) {
        unset($expression['length']);
        $expressionModel=new Expression(...holdsNull((array)$expression));
        if(strpos($expressionModel->getResult(),'data:image/png;base64,')!==false){
            $image=$expressionModel->getResult();
            $expressionModel->setResult("");
            $data['json']=array_merge($expressionModel->getAsAssociativeArray(),["diagram"=>$image]);
        }
        else{
            $data['json'][]=$expressionModel->getAsAssociativeArray();
        }
        

    }
    echo json_encode($data);
    
    
}
else if ($_SERVER['REQUEST_METHOD']==='GET') {

    if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])){
        echo json_encode($data);
    }
    else
    {
        $fileid=$_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'];
        $file_content=$db->get('expressions',[
            'file_id'=>$fileid
        ]);
        foreach ($file_content as $expression) {
            unset($expression['length']);
            $expressionModel=new Expression(...holdsNull((array)$expression));
            if(strpos($expressionModel->getResult(),'data:image/png;base64,')!==false){
                $image=$expressionModel->getResult();
                $expressionModel->setResult("");
                $data['json']=array_merge($expressionModel->getAsAssociativeArray(),["diagram"=>$image]);
            }
            else{
                $data['json'][]=$expressionModel->getAsAssociativeArray();
            }
        }
        echo json_encode($data);
    }
}
