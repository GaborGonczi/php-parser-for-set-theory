<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/rootfolder.php';

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])) {
    $location = rootfolder() . '/index.php';
    header("Location:$location");
    exit(1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="<?php echo rootfolder().'/src/js/app/client/page/files.js'; ?>" defer></script>
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

    <form action="<?php echo rootfolder().'/index.php'; ?>" method="post">
    <button id="back" name="client" type="submit">Vissza a főoldalra</button>
    </form>
</body>
</html>