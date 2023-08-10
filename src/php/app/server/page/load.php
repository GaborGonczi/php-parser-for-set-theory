<?php
require_once dirname(dirname(__FILE__)).'/db.php';
require_once dirname(dirname(__FILE__)).'client/constants.php';
require_once dirname(dirname(__FILE__)).'/class/model/Expression.php';
require_once dirname(dirname(__FILE__)).'/class/model/File.php';
require_once dirname(dirname(__FILE__)).'/class/model/Log.php';
require_once dirname(dirname(__FILE__)).'/class/model/User.php';

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
$fileContent = (array)json_decode(file_get_contents($_FILES['upload_file']['tmp_name']));
if(isset($fileContent['id'])){
    $isOwnFile=$db->isExist('files',['user_id'=>$user->getId()]);
    if($isOwnFile){
        $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']=$fileid;
        header("Location:$CONSTANTS[programUrl]");
    }
    else {
        $_SESSION['messages']['fileerror']='A fájl nem található';
    }

}