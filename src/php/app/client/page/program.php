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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo Rootfolder::getPath() . '/src/css/app/client/page/styles.css'; ?>" type="text/css">
    <script src="<?php echo Rootfolder::getPath() . '/src/js/external/mustache.js'; ?>"></script>
    <script type="module" src="<?php echo Rootfolder::getPath() . '/src/js/app/client/page/program.js'; ?>" defer></script>
    <?php if (isset($_SESSION) && isset($_SESSION['messages']) && isset($_SESSION['messages']['fileerror'])) { ?>
        <script >
            window.addEventListener("DOMContentLoaded",()=>{
                document.querySelector('#errors > textarea').innerHTML=<?php echo json_encode($_SESSION['messages']['fileerror']);?>;
                const backbtn=document.querySelector('#back');
                backbtn.addEventListener("click",()=>{
                    document.querySelector('#errors > textarea').innerHTML="";
                })
            });
           

            
        </script>
    <?php unset($_SESSION['messages']['fileerror']); } ?>
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo Rootfolder::getPath().'/src/favicon/apple-touch-icon.png'?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo Rootfolder::getPath().'/src/favicon/favicon-32x32.png'?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo Rootfolder::getPath().'/src/favicon/favicon-16x16.png'?>">
    <link rel="manifest" href="<?php echo Rootfolder::getPath().'/src/favicon/site.webmanifest'?>">
    <title>Program</title>
</head>

<body>
    <div class="wrapper">
    
            <div id="output"></div>
            

        <div id="right-toolbar-container" class="column toolbar">
            <div class="button-grid-container">
                <button title="legyen egyenlő" class="operator" value=":=">:=</button>
                <button title="eleme" class="operator" value="∈">∈</button>
                <button title="nem eleme" class="operator" value="∉">∉</button>
                <button title="egyenlő" class="operator" value="=">=</button>
                <button title="részhalmaz" class="operator" value="⊆">⊆</button>
                <button title="valódi részhalmaz" class="operator" value="⊂">⊂</button>
                <button title="komplementer" class="operator" value="∁">∁</button>
                <button title="unió" class="operator" value="∪">∪</button>
                <button title="metszet" class="operator" value="∩">∩</button>
                <button title="és" class="operator" value="∧">∧</button>
                <button title="vagy" class="operator" value="∨">∨</button>
                <button title="különbség" class="operator" value="∖"> ∖</button>
                <button title="osztja" class="operator" value="∣"> ∣</button>
                <button title="nem osztja" class="operator" value="∤"> ∤</button>
                <button title="számosság" class="operator" value="||">|S|</button>
            </div>
            <span>Matematikai mód</span>
            <label class="switch">
                <input type="checkbox">
                <span class="slider round"></span>
            </label>
            <span>Szöveges mód</span>
            <div id="variables" class="texts" ><textarea placeholder="Változók"></textarea></div>
            <div id="errors" class="texts" ><textarea placeholder="Hibaüzenetek"></textarea></div>
            <button id="download">Letöltés</button>
            <button id="print">Nyomtatás</button>
            <button id="new">Új</button>
            <div>
             <!--https://www.w3docs.com/snippets/css/how-to-customize-file-inputs.html-->
                <label class="customized-fileupload">
                    <input type="file" id="load" name="load">
                    <span>Munkalap betöltése</span>
                </label>
                <form id="backform" action="<?php echo Rootfolder::getPath().'/index.php'; ?>" method="post">
                 <button id="back" name="client" type="submit">Vissza a főoldalra</button>
                </form>
            </div>
           
        
           
        
        </div>
</body>

</html>