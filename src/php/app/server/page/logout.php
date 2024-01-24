<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
session_unset();
session_destroy();
$location=rootfolder().'/index.php';
header("Location:$location");
exit(0);