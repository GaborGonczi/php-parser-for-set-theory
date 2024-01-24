<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/autoloader.php';

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';

require_once dirname(dirname(__FILE__)).'/db.php';

use \app\server\classes\model\File;
use \app\server\classes\model\Expression;
use \app\server\classes\model\Log;
use \app\server\classes\model\User;
use \core\Regexp;

header('Content-Type: application/json');
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $location=rootfolder().'/index.php';
    header("Location:$location");
    exit(1);
}
else{
    $user=unserialize($_SESSION[$_COOKIE['PHPSESSID']]['authedUser']);
}
global $db;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if((new Regexp('(files\/get)'))->test($_SERVER['QUERY_STRING'])){
        $files=$db->get(['users','files'],['users.id'=>$user->getId(),'files.deleted_at'=>null],['files.user_id'=>'`users`.id'],'files.*');
        echo json_encode($files);
    }
    else if ((new Regexp('(files\/delete\/)([1-9][0-9]*)'))->test($_SERVER['QUERY_STRING'])) {
        $params=explode('/',$_SERVER['QUERY_STRING']);
        $id=intval(end($params));
        $expressions=$db->get('expressions',['file_id'=>$id]);
        foreach ($expressions as $expression) {
           $expression=(array)$expression;
           $data=$db->delete('expressions',date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),['id'=>$expression['id']]);
           if($data===false){
                $_SESSION['messages']['cruderror']='Hiba a művelet közben';
                $location=rootfolder().'/src/php/app/client/page/files.php';
                header("Location:$location"); 
                exit(0);  
           }
        }
        $data=$db->delete('files',date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),['id'=>$id]);
        if($data===false){
            $_SESSION['messages']['cruderror']='Hiba a művelet közben'; 
        }
        else{
            $_SESSION['messages']['crudsuccess']='A fájl sikeresen törölve'; 
        }
        $location=rootfolder().'/src/php/app/client/page/files.php';
        header("Location:$location");
        exit(0);        
    }
    else {
        $_SESSION['messages']['cruderror']="A(z) $_SERVER[QUERY_STRING] erőforrás nem található";
        $location=rootfolder().'/src/php/app/client/page/files.php';
        header("Location:$location");
        exit(0);       
    }
 
}