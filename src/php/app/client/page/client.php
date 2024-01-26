<?php


require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $location=rootfolder().'/index.php';
    header("Location:$location");
    exit(1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo rootfolder().'/src/favicon/apple-touch-icon.png'?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo rootfolder().'/src/favicon/favicon-32x32.png'?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo rootfolder().'/src/favicon/favicon-16x16.png'?>">
    <link rel="manifest" href="<?php echo rootfolder().'/src/favicon/site.webmanifest'?>">
    <title>Főoldal</title>
</head>
<body>
    <form action="<?php echo rootfolder().'/index.php'; ?>" method="post">
    <button id="help" name="help" type="submit" >Használati útmutató</button>
    <button id="program" name="program" type="submit">Program</button>
    <button id="questionnaire" name="questionnaire" type="submit">Kérdőív</button>
    <button id="files" name="files" type="submit">Fájljaim</button>
    <button id="logout" name="logout" type="submit">Kilépés</button>
    </form>
    <br/>
</body>
</html>