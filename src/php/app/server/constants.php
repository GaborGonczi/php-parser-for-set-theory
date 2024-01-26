<?php
require_once dirname(dirname(dirname(__FILE__))).'/rootfolder.php';

$BASEURL=rootfolder();
$CONSTANTS=[
    'register'=>$BASEURL.'/index.php?register',
    'login'=>$BASEURL.'/index.php?login'
];
