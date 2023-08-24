<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/autoloader.php';

require_once dirname(dirname(__FILE__)).'/db.php';

use \app\server\classes\model\File;
use \app\server\classes\model\Expression;
use \app\server\classes\model\Log;
use \app\server\classes\model\User;

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
$content=[];
$filefound=false;
if(isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])){
    $fileid=$_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'];
    $files=$db->get('files',['user_id'=>$user->getId()]);
    if($files){
        foreach ($files as $file) {
            $filemodel=new File(...array_values($file));
            if($filemodel->getId()==$fileid){
                $filefound=true;
                break;
            }
            
        }
        if($filefound){
            $isNotEmpty=$db->isExist('expressions',['file_id'=>$fileid]);
            $content['id']=$fileid;
            if($isNotEmpty){
                $expressions=$db->get('expressions',['file_id'=>$fileid]);
                foreach ($expressions as $expression) {
                    $content['expressions'][]=serialize(new Expression(...array_values($expression)));
                }
                echo json_encode($content);
            }
            else {
                $_SESSION['messages']['fileerror']='A fájl üres';
            }
        }
        else{
            $_SESSION['messages']['fileerror']='A fájl nem található';
        }
    }
    else{
        $_SESSION['messages']['fileerror']='A felhasználónak nincsenek fájljai.';
    }
}
else{
    $_SESSION['messages']['fileerror']='A fájlazonosító hiányzik.';
}