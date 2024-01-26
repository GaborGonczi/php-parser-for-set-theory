<?php
    require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo rootfolder().'/src/favicon/apple-touch-icon.png'?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo rootfolder().'/src/favicon/favicon-32x32.png'?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo rootfolder().'/src/favicon/favicon-16x16.png'?>">
    <link rel="manifest" href="<?php echo rootfolder().'/src/favicon/site.webmanifest'?>">
</head>
<body>
    <form action="<?php echo rootfolder().'/index.php'?>" method="post">
        <label for="username">Felhasználónév: </label>
        <input type="text" id="username" name="username" />
        <label for="password">Jelszó: </label>
        <input type="password" id="password" name="password" />
        <input type="submit" id="loginHandle" name="loginHandle" value="Bejelentkezés">
        <input type="submit" id="register" name="register" value="Regisztráció">
    </form>
    <?php if(isset($_SESSION)&&isset($_SESSION['messages'])&&isset($_SESSION['messages']['loginerror'])) { ?>
        <div><?php echo $_SESSION['messages']['loginerror']; ?> </div>
    <?php } ?>
</body>
</html>