<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/autoloader.php';

require_once dirname(dirname(__FILE__)).'/db.php';

use \app\server\classes\model\File;
use \app\server\classes\model\Expression;
use \app\server\classes\model\Log;
use \app\server\classes\model\User;
use \core\lib\datastructures\Map;

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
$data=['json'=>[],'variables'=>new Map([])];
unset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']);
if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])){
    $file=new File(null,$user->getId(),date('Y-m-d H:i:s',(new \DateTime('now'))->getTimestamp()),date('Y-m-d H:i:s',(new \DateTime('now'))->getTimestamp()),null);
    $id=$db->insert('files',$file->getAsAssociativeArray());
    if($id){
        $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']=$id;
        echo json_encode($data);
    }
}