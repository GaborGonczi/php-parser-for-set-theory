<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';
session_start();
if(isset($_SESSION)){
    if(isset($_SESSION['authedUser'])){
        unset($_SESSION['authedUser']);
    }
    if(isset($_SESSION['messages'])){
        unset($_SESSION['messages']);
    }
}
$location=rootfolder().'/index.php';
header("Location:$location");