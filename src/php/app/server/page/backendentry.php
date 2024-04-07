<?php

require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/autoloader.php';
//Helyhiány miatt az autoloader és a db.php betöltése valamint a use \utils\Rootfolder; és a use \app\server\classes\model\User; utasítások a dolgozat szövegében törlésre kerültek
use \utils\Rootfolder;

require_once dirname(dirname(__FILE__)) . '/db.php';

use \app\server\classes\model\User;

header('Content-Type: application/json');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])) {
    $location = Rootfolder::getPath() . '/index.php';
    header("Location:$location");
    exit(1);
}
if(isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $user=new User(...array_values(json_decode($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'],true)));
    $lang=$user->getLanguage();
}
else{
    $lang='hun';
}
$user=new User(...array_values(json_decode($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'],true)));
global $db;
$namespace="\\app\\server\\classes\\runnable\\";
$script=$_GET['page'];

$class=$namespace.mb_convert_case($script, MB_CASE_TITLE, "UTF-8")."Script";

echo (new $class($user,$db,$lang))->run();
