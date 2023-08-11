<?php
    require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
</head>
<body>
    <form action="index.php" method="post">
        <label for="username">Felhasználónév: </label>
        <input type="text" id="username" name="username" />
        <label for="password">Jelszó: </label>
        <input type="password" id="password" name="password" minlength="8" />
        <label for="passwordagain">Jelszó újra:</label>
        <input type="password" id="passwordagain" name="passwordagain" minlength="8" />
        <input type="submit" id="registerHandle" name="registerHandle" value="Regisztráció">
        <input type="submit" id="login" name="login" value="Vissza">
    </form>
    <?php if(isset($_SESSION)&&isset($_SESSION['messages'])&&isset($_SESSION['messages']['registererror'])) { ?>
        <div><?php echo $_SESSION['messages']['registererror']; ?> </div>
    <?php } ?>
</body>
</html>