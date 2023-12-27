<?php


require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';

session_start();
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
    <script type="module" src="<?php echo rootfolder().'/src/js/app/client/page/questionnaire.js';   ?>" defer></script>
    <title>Főoldal</title>
</head>
<body>
<form id="questionnaire"  method="post">
<input type="submit" value="Küldés">
</form>
<button id="back">Vissza a főoldalra</button>
</body>
</html>