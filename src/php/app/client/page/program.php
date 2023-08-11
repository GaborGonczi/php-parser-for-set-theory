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
    <link rel="stylesheet" href="<?php echo rootfolder().'/src/css/app/client/page/styles.css'; ?>" type="text/css">
    <script src="<?php echo rootfolder().'/src/js/external/mustache.js'; ?>"></script>
    <script type="module" src="<?php echo rootfolder().'/src/js/app/client/page/program.js'; ?>" defer></script>
    <title>Document</title>
</head>
<body>
    <div class="wrapper">
        <div id="content-container"       class="column resizable">
            <div id="output" disabled></div>
            <div id="input">
                <form method="post">
                    <textarea id="text"></textarea>
                    <input type="submit" hidden="hidden">
                </form>
            </div>
            
            
        </div>
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
                <button title="és" class="operator" value="ʌ">ʌ</button>
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
              <button id="download">Letöltés</button>
              <button id="print">Nyomtatás</button>
              <input type="file" id="load" name="load">
            

              <button id="back">Vissza a főoldalra</button>
    </div>
</body>
</html>