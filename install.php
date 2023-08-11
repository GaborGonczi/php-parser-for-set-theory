<?php

$path=__DIR__.'/.env';

$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {

    if (strpos(trim($line), '#') === 0) {
        continue;
    }

    list($name, $value) = explode('=', $line, 2);
    $name = trim($name);
    $value = trim($value);

    if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}
$installerInstance=null;

try {
    $installerInstance = new PDO ("mysql:host=" . getenv('HOST') . ";dbname=" . getenv('DB'),getenv('USER'),getenv('PASSWORD'));
} catch (PDOException $e) {
   echo $e->getMessage();
}

if($installerInstance){
    $installerInstance->exec(file_get_contents('sql.sql'));
    $seedStmt=$installerInstance->prepare(file_get_contents('seed.sql'));
    $seedStmt->execute(['system',password_hash(getenv('ADMIN_PASSWORD'),PASSWORD_BCRYPT),null,null,
    date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),null,null,null,null,null]);
    $installerInstance=null;
}
unlink('sql.sql');
unlink('seed.sql');
unlink(__FILE__);