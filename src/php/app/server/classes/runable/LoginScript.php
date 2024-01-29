<?php
namespace app\server\classes\runable;

use app\server\classes\Database;
use app\server\classes\model\User;
use \utils\Rootfolder;

class LoginScript extends Runable{
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

   return <<<REGISTER
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="apple-touch-icon" sizes=\"180x180" href="$rootfolderstring/src/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="$rootfolderstring/src/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="$rootfolderstring/src/favicon/favicon-16x16.png">
    <link rel="manifest" href="$rootfolderstring/src/favicon/site.webmanifest">
</head>
<body>
<form action="$rootfolderstring/index.php" method="post">
<label for="username">Felhasználónév: </label>
<input type="text" id="username" name="username" />
<label for="password">Jelszó: </label>
<input type="password" id="password" name="password" />
<input type="submit" id="loginHandle" name="loginHandle" value="Bejelentkezés">
<input type="submit" id="register" name="register" value="Regisztráció">
</form>
   $htmlmessage
</body>
</html>
REGISTER;
    }
}