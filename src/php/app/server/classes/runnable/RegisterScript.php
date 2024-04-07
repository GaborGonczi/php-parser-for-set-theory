<?php
namespace app\server\classes\runnable;
use app\server\classes\Database;
use app\server\classes\model\User;
use \utils\Rootfolder;
use \utils\Lang;

class RegisterScript extends Runnable{
    public function __construct( User $user= null, Database $db = null,string $lang='hun'){
        parent::__construct($user, $db,$lang);
    }
    public function run(): string{
        //https://www.phptutorial.net/php-tutorial/php-heredoc/
        $rootfolderstring=Rootfolder::getPath();
        $htmlmessage="";
        if(isset($_SESSION) && isset($_SESSION['messages']) && isset($_SESSION['messages']['registererror'])) { 
            $message=$_SESSION['messages']['registererror'];
            $htmlmessage="<div>$message</div>";
        }
        $usernameEng=Lang::getString('username','eng');
        $usernameHun=Lang::getString('username','hun');
        $passwordEng=Lang::getString('password','eng');
        $passwordHun=Lang::getString('password','hun');
        $passwordAgainEng=Lang::getString('passwordAgain','eng');
        $passwordAgainHun=Lang::getString('passwordAgain','hun');
        $backEng=Lang::getString('back','eng');
        $backHun=Lang::getString('back','hun');
        $registerEng=Lang::getString('register','eng');
        $registerHun=Lang::getString('register','hun');

   return <<<REGISTER
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link rel="apple-touch-icon" sizes=\"180x180" href="$rootfolderstring/src/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="$rootfolderstring/src/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="$rootfolderstring/src/favicon/favicon-16x16.png">
    <link rel="manifest" href="$rootfolderstring/src/favicon/site.webmanifest">
    <style>
    form > * {
        vertical-align: middle;
    }
    /* HIDE RADIO */
    #langs > label > input[type=radio] { 
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* IMAGE STYLES */
    #langs > label > img {
      cursor: pointer;
    }

    /* CHECKED STYLES */
    #langs > label > #englang:checked + img {
      outline: 2px solid #f00;
    }
    #langs > label > #hunlang:checked + img {
        outline: 2px solid #f00;
    }
   
    </style>
</head>
<body>
    <form action="$rootfolderstring/index.php" method="post">
        <label id="usernamelabel" for="username">Felhasználónév: </label>
        <input type="text" id="username" name="username" />
        <label id="passwordlabel" for="password">Jelszó: </label>
        <input type="password" id="password" name="password" minlength="8" />
        <label id="passwordagainlabel" for="passwordagain">Jelszó újra:</label>
        <input type="password" id="passwordagain" name="passwordagain" minlength="8" />
        <input type="submit" id="registerHandle" name="registerHandle" value="Regisztráció">
        <input type="submit" id="login" name="login" value="Vissza">
        <span id="langs">

        <label id="hunlabel" for="hunlang">
        <input type="radio" id="hunlang" name="lang" value="hun" />
        <img src="$rootfolderstring/src/lang-icon/hu.png" alt="Hungarian language" />
        </label>
        <label id="englabel" for="englang">
        <input type="radio" id="englang" name="lang" value="eng" />
        <img src="$rootfolderstring/src/lang-icon/gb.png" alt="English language" />
        </label>
        </span>
    </form>
   $htmlmessage
   
   <script>
   const usernamelabel=document.querySelector('#usernamelabel');
   const passwordlabel=document.querySelector('#passwordlabel');
   const passwordagainlabel=document.querySelector('#passwordagainlabel');
   const registerbtn=document.querySelector('#registerHandle');
   const loginbtn=document.querySelector('#login');
   const englang=document.querySelector('#englang');
   const hunlang=document.querySelector('#hunlang');
   window.addEventListener('DOMContentLoaded',e=>{
        englang.addEventListener('change',e=>{
            if(englang.checked){
                hunlang.checked=false;
                usernamelabel.innerText='$usernameEng';
                passwordlabel.innerText='$passwordEng';
                passwordagainlabel.innerText='$passwordAgainEng';
                loginbtn.value='$backEng';
                registerbtn.value='$registerEng';
            }
       
        })
        hunlang.addEventListener('change',e=>{
            if(hunlang.checked){
                englang.checked=false;
                usernamelabel.innerText='$usernameHun';
                passwordlabel.innerText='$passwordHun';
                passwordagainlabel.innerText='$passwordAgainHun';
                loginbtn.value='$backHun';
                registerbtn.value='$registerHun';
            }
        })
        hunlang.checked=true;
        englang.checked=false;
        usernamelabel.innerText='$usernameHun';
        passwordlabel.innerText='$passwordHun';
        loginbtn.value='$backHun';
        registerbtn.value='$registerHun';
   })
   </script>
</body>
</html>
REGISTER;
    }
}
    

