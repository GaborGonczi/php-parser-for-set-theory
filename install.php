<?php

require_once 'autoloader.php';

require_once __DIR__.'/environment.php';

use \app\server\classes\Env;

(new Env(dirname(__FILE__).'/.env',constant('DEV')))->load();

$installerInstance=null;

try {
    $installerInstance = new PDO ("mysql:host=" . getenv('HOST') . ";dbname=" . getenv('DB'),getenv('USER'),getenv('PASSWORD'));
} catch (PDOException $e) {
   echo $e->getMessage();
}

if($installerInstance){
    $installerInstance->exec(file_get_contents('parser.sql'));
    $seedStmt=$installerInstance->prepare(file_get_contents('seed.sql'));
    $seedStmt->execute(['system',password_hash(getenv('ADMIN_PASSWORD'),PASSWORD_BCRYPT),null,null,'hun',
    date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),null,null,null,null,null]);
    $installerInstance=null;
}
echo "Installation completed successfully.";
unlink('parser.sql');
unlink('seed.sql');
unlink(__FILE__);