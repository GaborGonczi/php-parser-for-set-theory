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
    <title><?php echo Lang::getString('clientTitle',$lang);?></title>
</head>
<body>
    <form action="<?php echo Rootfolder::getPath().'/index.php'; ?>" method="post">
    <button id="help" name="help" type="submit" ><?php echo Lang::getString('helpButton',$lang);?></button>
    <button id="program" name="program" type="submit"><?php echo Lang::getString('programButton',$lang);?></button>
    <button id="questionnaire" name="questionnaire" type="submit"><?php echo Lang::getString('questionnaireButton',$lang);?></button>
    <button id="files" name="files" type="submit"><?php echo Lang::getString('filesButton',$lang);?></button>
    <button id="automatons" name="automatons" type="submit"><?php echo Lang::getString('automatonsButton',$lang);?></button>
    <button id="profile" name="profile" type="submit"><?php echo Lang::getString('profileButton',$lang);?></button>
    <button id="logout" name="logout" type="submit"><?php echo Lang::getString('logoutButton',$lang);?></button>
    </form>
    <br/>
</body>
</html>