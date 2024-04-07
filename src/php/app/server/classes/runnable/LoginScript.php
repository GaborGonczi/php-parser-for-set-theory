<?php
namespace app\server\classes\runnable;

use app\server\classes\Database;
use app\server\classes\model\User;
use \utils\Rootfolder;

class LoginScript extends Runnable{
    public function __construct( User $user= null, Database $db = null){
        parent::__construct($user, $db);
    }
    public function run():string {
        //https://www.phptutorial.net/php-tutorial/php-heredoc/
        $rootfolderstring=Rootfolder::getPath();
        $htmlmessage="";
        if(isset($_SESSION) && isset($_SESSION['messages']) && isset($_SESSION['messages']['loginerror'])) { 
            $message=$_SESSION['messages']['loginerror'];
            $htmlmessage="<div>$message</div>";
        }
        //logo positioning https://www.youtube.com/watch?v=rhPSo4_Tgi0&ab_channel=WebDevSimplified
   return <<<LOGIN
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="apple-touch-icon" sizes="180x180" href="$rootfolderstring/src/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="$rootfolderstring/src/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="$rootfolderstring/src/favicon/favicon-16x16.png">
    <link rel="manifest" href="$rootfolderstring/src/favicon/site.webmanifest">
    <style>
    form > * {
        vertical-align: middle;
    }
    img{
        height:20px;
    }
    </style>
</head>
<body>
<form action="$rootfolderstring/index.php" method="post">
<label for="username">Felhasználónév: </label>
<input type="text" id="username" name="username" />
<label for="password">Jelszó: </label>
<input type="password" id="password" name="password" />
<input type="submit" id="loginHandle" name="loginHandle" value="Bejelentkezés">
<input type="submit" id="register" name="register" value="Regisztráció">
<a href="https://github.com/GaborGonczi/php-parser-for-set-theory">
<img src="$rootfolderstring/src/github-icon/github-mark.svg" alt="Source code">
</a>
</form>
   $htmlmessage
</body>
</html>
LOGIN;
    }
}