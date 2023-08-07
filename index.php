<?php

require_once 'src/php/app/server/class/Env.php';
require_once 'src/php/app/server/class/Config.php';
require_once 'src/php/app/server/class/Database.php';
require_once 'src/php/app/server/class/Auth.php';
require_once 'src/php/app/server/class/Application.php';
$page='';
session_start();
(new Env(__DIR__.'/.env'))->load();
$db=new Database(new Config(getenv('HOST'),getenv('DB'),getenv('USER'),getenv('PASSWORD')));
if(isset($_SESSION['authedUser'])){
    $appPages=array();
    $app=new Application($_SESSION['authedUser'],$db);
    if(isset($_POST['page'])){
        $page=$_POST['page'];
        if(in_array($page,$appPages)){
            $app->$page();
        }
        else{
            $app->client();
        }
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
    if($page===''){
        $page='login';
    }
    $auth->$page();
    

   
}