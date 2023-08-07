<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';
session_start();
session_unset();
session_destroy();
$location=rootfolder().'/index.php';
header("Location:$location");