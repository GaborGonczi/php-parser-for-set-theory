<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/autoloader.php';

use \utils\Rootfolder;
use \utils\Lang;
use \app\server\classes\model\User;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])) {
    $location = Rootfolder::getPath() . '/index.php';
    header("Location:$location");
    exit(1);
}
if(isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $user=new User(...array_values(json_decode($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'],true)));
    $lang=$user->getLanguage();
}
else{
    $lang='hun';
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
        <script>
            window.addEventListener("DOMContentLoaded", () => {
                document.querySelector('#errors > textarea').innerHTML = <?php echo json_encode($_SESSION['messages']['fileerror']); ?>;
                const backbtn = document.querySelector('#back');
                backbtn.addEventListener("click", () => {
                    document.querySelector('#errors > textarea').innerHTML = "";
                })
            });



        </script>
        <?php unset($_SESSION['messages']['fileerror']);
    }
    ?>
    <link rel="apple-touch-icon" sizes="180x180"
        href="<?php echo Rootfolder::getPath() . '/src/favicon/apple-touch-icon.png' ?>">
    <link rel="icon" type="image/png" sizes="32x32"
        href="<?php echo Rootfolder::getPath() . '/src/favicon/favicon-32x32.png' ?>">
    <link rel="icon" type="image/png" sizes="16x16"
        href="<?php echo Rootfolder::getPath() . '/src/favicon/favicon-16x16.png' ?>">
    <link rel="manifest" href="<?php echo Rootfolder::getPath() . '/src/favicon/site.webmanifest' ?>">
    <title><?php echo Lang::getString('programTitle',$lang);  ?></title>
</head>

<body>
    <div class="wrapper">

        <div id="output"></div>


        <div id="right-toolbar-container" class="column toolbar">
            <div class="button-grid-container">
                <button id="tobeequal" title="<?php echo Lang::getString('toBeEqual',$lang);  ?>" class="operator" value=":=">:=</button>
                <button id="elementof" title="<?php echo Lang::getString('elementOf',$lang);  ?>" class="operator" value="∈">∈</button>
                <button id="notelementof" title="<?php echo Lang::getString('notElementOf',$lang);  ?>" class="operator" value="∉">∉</button>
                <button id="equal" title="<?php echo Lang::getString('equal',$lang);  ?>" class="operator" value="=">=</button>
                <button id="subsetof" title="<?php echo Lang::getString('subsetOf',$lang);  ?>" class="operator" value="⊆">⊆</button>
                <button id="realsubsetof" title="<?php echo Lang::getString('realSubsetOf',$lang);  ?>" class="operator" value="⊂">⊂</button>
                <button id="complement" title="<?php echo Lang::getString('complement',$lang);  ?>" class="operator" value="∁">∁</button>
                <button id="union" title="<?php echo Lang::getString('union',$lang);  ?>" class="operator" value="∪">∪</button>
                <button id="intersection" title="<?php echo Lang::getString('intersection',$lang);  ?>" class="operator" value="∩">∩</button>
                <button id="logicaland" title="<?php echo Lang::getString('logicalAnd',$lang);  ?>" class="operator" value="∧">∧</button>
                <button id="logicalor" title="<?php echo Lang::getString('logicalOr',$lang);  ?>" class="operator" value="∨">∨</button>
                <button id="setminus" title="<?php echo Lang::getString('setminus',$lang);  ?>" class="operator" value="∖"> ∖</button>
                <button id="divides" title="<?php echo Lang::getString('divides',$lang);  ?>" class="operator" value="∣"> ∣</button>
                <button id="doesnotdivide" title="<?php echo Lang::getString('doesNotDivide',$lang);  ?>" class="operator" value="∤"> ∤</button>
                <button id="cardinality" title="<?php echo Lang::getString('cardinality',$lang);  ?>" class="operator" value="||">|S|</button>
            </div>
            <div>
            <span><?php echo Lang::getString('mathematicalMode',$lang);  ?></span>
            <label class="switch">
                <input title="<?php echo Lang::getString('mode',$lang); ?>" id="mode" type="checkbox">
                <span class="slider round"></span>
            </label>
            <span><?php echo Lang::getString('textMode',$lang);  ?></span>
            </div>
            <div>
            <span><?php echo Lang::getString('userErrorMessages',$lang);  ?></span>
            <label class="switch">
                <input title="<?php echo Lang::getString('errorMessages',$lang); ?>" id="errorMessages" type="checkbox">
                <span class="slider round"></span>
            </label>
            <span><?php echo Lang::getString('developerErrorMessages',$lang);?></span>
            </div>
            <div id="variables" class="texts"><textarea placeholder="<?php echo Lang::getString('variables',$lang);  ?>" disabled></textarea></div>
            <div id="errors" class="texts"><textarea placeholder="<?php echo Lang::getString('errors',$lang);  ?>" disabled></textarea></div>
            <button title="<?php echo Lang::getString('download',$lang); ?>" id="download"><?php echo Lang::getString('download',$lang);  ?></button>
            <button title="<?php echo Lang::getString('print',$lang); ?>" id="print"><?php echo Lang::getString('print',$lang);  ?></button>
            <button title="<?php echo Lang::getString('new',$lang);?>" id="new"><?php echo Lang::getString('new',$lang);  ?></button>
            <span><?php echo Lang::getString('dfaOff',$lang);  ?></span>
            <label class="switch">
                <input title="<?php Lang::getString('dfa',$lang); ?>" id="dfa" type="checkbox" disabled>
                <span class="slider round"></span>
            </label>
            <span><?php echo Lang::getString('dfaOn',$lang);  ?></span>
            <div>
                <!--https://www.w3docs.com/snippets/css/how-to-customize-file-inputs.html-->
                <label class="customized-fileupload">
                    <input title="<?php echo Lang::getString('load',$lang);  ?>" type="file" id="load" name="load">
                    <span><?php echo Lang::getString('load',$lang);  ?></span>
                </label>
                <form id="backform" action="<?php echo Rootfolder::getPath().'/index.php'; ?>" method="post">
                    <button id="back" name="client" type="submit"><?php echo Lang::getString('backToTheMainPage',$lang);  ?></button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>