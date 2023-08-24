<?php

use \app\server\classes\Env;
use \app\server\classes\Database;
use \app\server\classes\Config;
(new Env(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/.env'))->load();
$db=new Database(new Config(getenv('HOST'),getenv('DB'),getenv('USER'),getenv('PASSWORD')));