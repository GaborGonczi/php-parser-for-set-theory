<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/autoloader.php';

use \utils\Rootfolder;

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])) {
    $location = Rootfolder::getPath() . '/index.php';
    header("Location:$location");
    exit(1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="<?php echo Rootfolder::getPath().'/src/js/app/client/page/files.js'; ?>" defer></script>
    <style>
        table{
            position: relative;
            width: 100%;
            border: solid 1px black;
            border-collapse:collapse;
        }
        tr,td{
            border: solid 1px black;
        }
    </style>
     <link rel="apple-touch-icon" sizes="180x180" href="<?php echo Rootfolder::getPath().'/src/favicon/apple-touch-icon.png'?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo Rootfolder::getPath().'/src/favicon/favicon-32x32.png'?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo Rootfolder::getPath().'/src/favicon/favicon-16x16.png'?>">
    <link rel="manifest" href="<?php echo Rootfolder::getPath().'/src/favicon/site.webmanifest'?>">
    <title>Fájljaim</title>
</head>
<body>
    <div class="container" id="container">
    </div>
    <?php if(isset($_SESSION)&&isset($_SESSION['messages'])&&isset($_SESSION['messages']['cruderror'])) { ?>
        <div><?php echo $_SESSION['messages']['cruderror']; unset($_SESSION['messages']['cruderror']);?> </div>
    <?php } ?>
    <?php if(isset($_SESSION)&&isset($_SESSION['messages'])&&isset($_SESSION['messages']['crudsuccess'])) { ?>
        <div><?php echo $_SESSION['messages']['crudsuccess']; unset($_SESSION['messages']['crudsuccess']);?> </div>
    <?php } ?>

    <form action="<?php echo Rootfolder::getPath().'/index.php'; ?>" method="post">
    <button id="back" name="client" type="submit">Vissza a főoldalra</button>
    </form>
</body>
</html>