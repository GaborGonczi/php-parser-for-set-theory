<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/autoloader.php';

use \utils\Rootfolder;
use \utils\Lang;
use \app\server\classes\model\User;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset ($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])) {
    $location = Rootfolder::getPath() . '/index.php';
    header("Location:$location");
    exit (1);
}
if (isset ($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])) {
    $user = new User(...array_values(json_decode($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'], true)));
    $lang = $user->getLanguage();
} else {
    $lang = 'hun';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        table {
            position: relative;
            width: 100%;
            border: solid 1px black;
            border-collapse: collapse;
            margin: 5px 0;
        }

        tr,
        td {
            border: solid 1px black;
        }

        .hide {
            display: none;
        }
    </style>
    <script type="module" src="<?php echo Rootfolder::getPath() . '/src/js/app/client/page/files.js'; ?>" defer></script>
    <link rel="apple-touch-icon" sizes="180x180"
        href="<?php echo Rootfolder::getPath() . '/src/favicon/apple-touch-icon.png' ?>">
    <link rel="icon" type="image/png" sizes="32x32"
        href="<?php echo Rootfolder::getPath() . '/src/favicon/favicon-32x32.png' ?>">
    <link rel="icon" type="image/png" sizes="16x16"
        href="<?php echo Rootfolder::getPath() . '/src/favicon/favicon-16x16.png' ?>">
    <link rel="manifest" href="<?php echo Rootfolder::getPath() . '/src/favicon/site.webmanifest' ?>">
    <title>
        <?php echo Lang::getString('filesTitle', $lang); ?>
    </title>
</head>

<body>
    <div class="container hide" id="container">
        <details id="my" open>
            <summary>
                <?php echo Lang::getString('myFiles', $lang); ?>
            </summary>
        </details>
        <details id="others">
            <summary>
                <?php echo Lang::getString('sharedFiles', $lang); ?>
            </summary>
        </details>
    </div>

    <div id="message"> </div>



    <form action="<?php echo Rootfolder::getPath() . '/index.php'; ?>" method="post">
        <button id="back" name="client" type="submit">
            <?php echo Lang::getString('backToTheMainPage', $lang); ?>
        </button>
    </form>
</body>

</html>