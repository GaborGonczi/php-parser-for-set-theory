<?php

/**
* A file that uses the Env, Database, and Config classes to load environment variables and create a database connection.
*
* @package app\server\classes
*/

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/environment.php';

use \app\server\classes\Env;
use \app\server\classes\Database;
use \app\server\classes\Config;
(new Env(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/.env',constant('DEV')))->load();
$db=new Database(new Config(getenv('HOST'),getenv('DB'),getenv('USER'),getenv('PASSWORD')));