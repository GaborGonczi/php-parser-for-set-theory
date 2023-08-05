<?php

$db=null;

try {
    $db = new PDO ("mysql:host=" . getenv('HOST') . ";dbname=" . getenv('DB'),getenv('USER'),getenv('PASSWORD'));
} catch (PDOException $e) {
   echo $e->getMessage();
}

if($db){
    $db->exec(file_get_contents('sql.sql'));
    $db=null;
}
unlink('sql.sql');