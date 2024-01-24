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
    <script type="module" src="<?php echo rootfolder().'/src/js/app/client/page/help.js'; ?>" defer></script>
    <title>Használati útmutató</title>
</head>
<body>
    <h1>Használati útmutató</h1>

    <div><iframe src="" width="640" height="480" allow="autoplay"></iframe></div>
    
    <form action="<?php echo rootfolder().'/index.php'; ?>" method="post">
    <button id="back" name="client" type="submit">Vissza a főoldalra</button>
    </form>
</body>
</html>