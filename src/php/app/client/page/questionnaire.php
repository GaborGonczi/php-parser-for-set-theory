<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/autoloader.php';

use app\client\questionnaire\Likert;
use app\client\questionnaire\LongAnswer;
use app\client\questionnaire\MultipleChoice;
use app\client\questionnaire\MultipleChoiceMatrix;
use app\client\questionnaire\ShortAnswer;
use app\client\questionnaire\SingleChoice;
use app\client\questionnaire\SingleChoiceMatrix;

use \utils\Rootfolder;
use \app\client\questionnaire\Questionnaire;

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $location=Rootfolder::getPath().'/index.php';
    header("Location:$location");
    exit(1);
}
if (isset($_SESSION) && isset($_SESSION['messages']) && !isset($_SESSION['messages']['questionnairemessage_success'])) {
    $title="Halmazok témakörének tanulását/tanítását segítő program";
    $description=<<<DESC
    Gönczi Gábor vagyok a Széchenyi István Egyetem végzős hallgatója.<br/> \n 
    Az alábbi kérdőívben az általam készített programhoz kapcsolódó kérdések találhatók. \n
    A kérdőív teljesen anonim.<br/> \n
    Köszönöm ha a  kitöltésével segíti a diplomamunkám elkészítését.
    DESC;
    $questionnaire= new Questionnaire($title,$description);
    $questionnaire->addQuestion(new ShortAnswer("average_daily_usage_in_minute","Átlagosan mennyi ideig használta a programot naponta (percben mérve)?",true));
    $questionnaire->addQuestion(new Likert("program_conductiveness","Ön szerint mennyire hasznos a program?",true,
    [
        "1"=>"1",
        "2"=>"2",
        "3"=>"3",
        "4"=>"4",
        "5"=>"5"
    ],
    "Egyáltalán nem hasznos","Nagyon hasznos"));
    $questionnaire->addQuestion(new SingleChoiceMatrix("user_satisfaction","Mennyire elégedett a program következő jellemzőivel?",true,
        [
            "design"=>[
                "label"=>"Dizájn",
                "code"=>"design",
                "values"=>[
                    "1"=>"1",
                    "2"=>"2",
                    "3"=>"3",
                    "4"=>"4",
                    "5"=>"5"
                ]
            ],
            "tutorial"=>[
                "label"=>"A használatot bemutató videóval",
                "code"=>"tutorial",
                "values"=>[
                    "1"=>"1",
                    "2"=>"2",
                    "3"=>"3",
                    "4"=>"4",
                    "5"=>"5"
                ]
            ],
            "example"=>[
                "label"=>"A halmazok témakörét feldolgozó mintafájllal",
                "code"=>"example",
                "values"=>[
                    "1"=>"1",
                    "2"=>"2",
                    "3"=>"3",
                    "4"=>"4",
                    "5"=>"5"
                ]
            ],
            "errormessages"=>[
                "label"=>"A hibaüzenetek egyértelműségével",
                "code"=>"errormessages",
                "values"=>[
                    "1"=>"1",
                    "2"=>"2",
                    "3"=>"3",
                    "4"=>"4",
                    "5"=>"5"
                ]
            ],
            "curriculumsize"=>[
                "label"=>"A gyakorolható tananyagrész nagyságával",
                "code"=>"curriculumsize",
                "values"=>[
                    "1"=>"1",
                    "2"=>"2",
                    "3"=>"3",
                    "4"=>"4",
                    "5"=>"5"
                ]
            ],

        ]
    ));
    $questionnaire->addQuestion(new LongAnswer("extend_program","Ha van olyan része a programnak, amin változtatna vagy olyan funkció, amivel kibővítené a programot, akkor kérem írja le ezt néhány mondatban.",true));
    $questionnaire->addQuestion(new MultipleChoiceMatrix("program_usage_time_of_the_day","Mikor használta a programot általában?",true,
        [
            "weekdays"=>[
                "label"=>"Hétköznap",
                "code"=>"weekdays",
                "values"=>[
                   "morning"=>"Reggel",
                   "beforenoon"=>"Délelőtt",
                   "afternoon"=>"Délután"
                ]
            ],
            "weekends"=>[
                "label"=>"Hétvégén",
                "code"=>"weekends",
                "values"=>[
                   "morning"=>"Reggel",
                   "beforenoon"=>"Délelőtt",
                   "afternoon"=>"Délután"
                ]
            ]
        ]
    ));
    $questionnaire->addQuestion(new SingleChoice("recommendation_to_try","Ajánlaná ismerősének?",true,
    [
        "y"=>"Igen",
        "n"=>"Nem"
    ]
    ));
    $questionnaire->addQuestion(new LongAnswer("recommendation_to_try_desc","Kérem fejtse ki az előző kérdésre adott válaszát.",true));
    $questionnaire->addQuestion(new ShortAnswer("math_knnowledge_by_mark","Érdemjegyei alapján milyen az Ön matematikatudása?",true));
    $questionnaire->addQuestion(new SingleChoice("math_knowledge_after_program_usage","Ön szerint javult a matematika tudása a program használatával?",true,
    [
        "y"=>"Igen",
        "n"=>"Nem"
    ]
    ));
    $questionnaire->addQuestion(new SingleChoice("gender","Neme?",true,
    [
        "f"=>"Nő",
        "m"=>"Férfi"
    ]
    ));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo Rootfolder::getPath().'/src/favicon/apple-touch-icon.png'?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo Rootfolder::getPath().'/src/favicon/favicon-32x32.png'?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo Rootfolder::getPath().'/src/favicon/favicon-16x16.png'?>">
    <link rel="manifest" href="<?php echo Rootfolder::getPath().'/src/favicon/site.webmanifest'?>">
    <style>
        .container{
            width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        div > *{
            margin-top:5px;
            margin-bottom: 5px;
            width: 100%;
        }
    </style>
    <title>Kérdőív</title>
</head>
<body>

<?php 
if (isset($_SESSION) && isset($_SESSION['messages']) && !isset($_SESSION['messages']['questionnairemessage_success'])) {
    if(isset($_SESSION['messages']['questionnairemessage_error'])){
        echo "<div>$_SESSION[messages][questionnairemessage_error]</div>\n";
    }
    echo $questionnaire->getHTML();
}
else if(isset($_SESSION) && isset($_SESSION['messages']) && isset($_SESSION['messages']['questionnairemessage_success'])) {
    echo "<div>$_SESSION[messages][questionnairemessage_success]</div>\n";
}
else {
    echo "<div>Ismeretlen hiba történt!</div>\n";
}
?>

<form action="<?php echo Rootfolder::getPath().'/index.php'; ?>" method="post">
    <button id="back" name="client" type="submit">Vissza a főoldalra</button>
</form>
</body>
</html>