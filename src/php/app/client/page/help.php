<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/autoloader.php';

use \utils\Rootfolder;
use \utils\Lang;
use \app\server\classes\model\User;

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $location=Rootfolder::getPath().'/index.php';
    header("Location:$location");
    exit(1);
}
if(isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $user=new User(...array_values(json_decode($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'],true)));
    $lang=$user->getLanguage();
}
else{
    $lang='hun';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo Rootfolder::getPath().'/src/favicon/apple-touch-icon.png'?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo Rootfolder::getPath().'/src/favicon/favicon-32x32.png'?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo Rootfolder::getPath().'/src/favicon/favicon-16x16.png'?>">
    <link rel="manifest" href="<?php echo Rootfolder::getPath().'/src/favicon/site.webmanifest'?>">
    <title><?php echo Lang::getString('helpTitle',$lang);  ?></title>
</head>
<body>
    <h1><?php echo Lang::getString('helpTitle'); ?></h1>

    <div><iframe src="" width="640" height="480" allow="autoplay"></iframe></div>
    
    <form action="<?php echo Rootfolder::getPath().'/index.php'; ?>" method="post">
    <button id="back" name="client" type="submit"><?php echo Lang::getString('backToTheMainPage',$lang);  ?></button>
    </form>
</body>
</html>