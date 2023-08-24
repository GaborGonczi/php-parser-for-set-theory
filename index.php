<?php
require_once 'autoloader.php';

require_once 'src/php/app/server/db.php';

use \app\server\classes\Application;
use \app\server\classes\Auth;

$page='';
session_start();
global $db;
if(isset($_COOKIE['PHPSESSID'])&&isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $user=unserialize($_SESSION[$_COOKIE['PHPSESSID']]['authedUser']);
    $app=new Application($user,$db);
    $appPages=array('client','help','program');
    for ($i=0; $i <count($appPages); $i++) {
        if(isset($_POST[$appPages[$i]])){
            $page=$appPages[$i];
            break;
        }
    }
    if($page){
        $app->$page();
    }
    else{
        $app->client();
    }
}
else{
    $auth=new Auth($db);
    $authPages=array('login','loginHandle','register','registerHandle');
    for ($i=0; $i <count($authPages); $i++) {
        if(isset($_POST[$authPages[$i]])){
            $page=$authPages[$i];
            break;
        }
    }
    if($page){
        $auth->$page();
    }
    else{
        $auth->login();
    }
   
}