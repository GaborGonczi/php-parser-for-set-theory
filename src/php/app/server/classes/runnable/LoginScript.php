<?php
namespace app\server\classes\runnable;

use app\server\classes\Database;
use app\server\classes\model\User;
use utils\Lang;
use \utils\Rootfolder;

class LoginScript extends Runnable{
    public function __construct( User $user= null, Database $db = null,string $lang='hun'){
        parent::__construct($user, $db,$lang);
    }
    public function run():string {
        //https://www.phptutorial.net/php-tutorial/php-heredoc/
        $rootfolderstring=Rootfolder::getPath();
        $htmlmessage="";
        if(isset($_SESSION) && isset($_SESSION['messages']) && isset($_SESSION['messages']['loginerror'])) { 
            $message=$_SESSION['messages']['loginerror'];
            $htmlmessage="<div>$message</div>";
        }
        $usernameEng=Lang::getString('username','eng');
        $usernameHun=Lang::getString('username','hun');
        $passwordEng=Lang::getString('password','eng');
        $passwordHun=Lang::getString('password','hun');
        $loginEng=Lang::getString('login','eng');
        $loginHun=Lang::getString('login','hun');
        $registerEng=Lang::getString('register','eng');
        $registerHun=Lang::getString('register','hun');

        //logo positioning https://www.youtube.com/watch?v=rhPSo4_Tgi0&ab_channel=WebDevSimplified
        // language select https://stackoverflow.com/questions/37582952/using-a-background-image-for-checkbox https://stackoverflow.com/questions/29346385/hide-radio-button-while-keeping-its-functionality
   return <<<LOGIN
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="apple-touch-icon" sizes="180x180" href="$rootfolderstring/src/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="$rootfolderstring/src/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="$rootfolderstring/src/favicon/favicon-16x16.png">
    <link rel="manifest" href="$rootfolderstring/src/favicon/site.webmanifest">
    <style>
    form > * {
        vertical-align: middle;
    }
    #github{
        height:20px;
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
<label id="usernamelabel" for="username">: </label>
<input type="text" id="username" name="username" />
<label id="passwordlabel" for="password">: </label>
<input type="password" id="password" name="password" />
<input type="submit" id="loginHandle" name="loginHandle" value="">
<input type="submit" id="register" name="register" value="">
<a href="https://github.com/GaborGonczi/php-parser-for-set-theory">
<img id="github" src="$rootfolderstring/src/github-icon/github-mark.svg" alt="Source code">
</a>
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
   const loginbtn=document.querySelector('#loginHandle');
   const registerbtn=document.querySelector('#register');
   const englang=document.querySelector('#englang');
   const hunlang=document.querySelector('#hunlang');
   window.addEventListener('DOMContentLoaded',e=>{
        englang.addEventListener('change',e=>{
            if(englang.checked){
                hunlang.checked=false;
                usernamelabel.innerText='$usernameEng';
                passwordlabel.innerText='$passwordEng';
                loginbtn.value='$loginEng';
                registerbtn.value='$registerEng';
            }
       
        })
        hunlang.addEventListener('change',e=>{
            if(hunlang.checked){
                englang.checked=false;
                usernamelabel.innerText='$usernameHun';
                passwordlabel.innerText='$passwordHun';
                loginbtn.value='$loginHun';
                registerbtn.value='$registerHun';
            }
        })
        hunlang.checked=true;
        englang.checked=false;
        usernamelabel.innerText='$usernameHun';
        passwordlabel.innerText='$passwordHun';
        loginbtn.value='$loginHun';
        registerbtn.value='$registerHun';
   })
   </script>
</body>
</html>
LOGIN;
    }
}