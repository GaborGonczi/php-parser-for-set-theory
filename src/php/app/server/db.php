<?php
require_once dirname(__FILE__).'/class/Env.php';
require_once dirname(__FILE__).'/class/Config.php';
require_once dirname(__FILE__).'/class/Database.php';
(new Env(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/.env'))->load();
$db=new Database(new Config(getenv('HOST'),getenv('DB'),getenv('USER'),getenv('PASSWORD')));