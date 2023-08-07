<?php
    require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
</head>
<body>
    <form action="index.php" method="post">
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