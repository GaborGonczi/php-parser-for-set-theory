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
use \utils\Lang;
use \app\server\classes\model\User;
use \app\client\questionnaire\Questionnaire;

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $location=Rootfolder::getPath().'/index.php';
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
if (isset($_SESSION) && isset($_SESSION['messages']) && !isset($_SESSION['messages']['questionnairemessage_success'])) {
    $title=Lang::getString('questionnaireTitle',$lang);
    $description=Lang::getString('questionnaireDescription',$lang); 
    $questionnaire= new Questionnaire($title,$description);
    $questionnaire->addQuestion(new ShortAnswer('average_daily_usage_in_minute',Lang::getString('questionnaireAverageDailyUsageInMinute',$lang),true));
    $questionnaire->addQuestion(new Likert('program_conductiveness',Lang::getString('questionnaireProgramConductiveness',$lang),true,
    [
        '1'=>'1',
        '2'=>'2',
        '3'=>'3',
        '4'=>'4',
        '5'=>'5'
    ],
    Lang::getString('questionnaireProgramConductivenessLow',$lang),Lang::getString('questionnaireProgramConductivenessHigh',$lang)));
    $questionnaire->addQuestion(new SingleChoiceMatrix('user_satisfaction',Lang::getString('questionnaireUserSatisfaction',$lang),true,
        [
            'design'=>[
                'label'=>Lang::getString('questionnaireUserSatisfactionDesign',$lang),
                'code'=>'design',
                'values'=>[
                    '1'=>'1',
                    '2'=>'2',
                    '3'=>'3',
                    '4'=>'4',
                    '5'=>'5'
                ]
            ],
            'tutorial'=>[
                'label'=>Lang::getString('questionnaireUserSatisfactionTutorial',$lang),
                'code'=>'tutorial',
                'values'=>[
                    '1'=>'1',
                    '2'=>'2',
                    '3'=>'3',
                    '4'=>'4',
                    '5'=>'5'
                ]
            ],
            'example'=>[
                'label'=>Lang::getString('questionnaireUserSatisfactionExample',$lang),
                'code'=>'example',
                'values'=>[
                    '1'=>'1',
                    '2'=>'2',
                    '3'=>'3',
                    '4'=>'4',
                    '5'=>'5'
                ]
            ],
            'errormessages'=>[
                'label'=>Lang::getString('questionnaireUserSatisfactionErrormessages',$lang),
                'code'=>'errormessages',
                'values'=>[
                    '1'=>'1',
                    '2'=>'2',
                    '3'=>'3',
                    '4'=>'4',
                    '5'=>'5'
                ]
            ],
            'curriculumsize'=>[
                'label'=>Lang::getString('questionnaireUserSatisfactionCurriculumsize',$lang),
                'code'=>'curriculumsize',
                'values'=>[
                    '1'=>'1',
                    '2'=>'2',
                    '3'=>'3',
                    '4'=>'4',
                    '5'=>'5'
                ]
            ],

        ]
    ));
    $questionnaire->addQuestion(new LongAnswer('extend_program',Lang::getString('questionnaireExtendProgram',$lang),true));
    $questionnaire->addQuestion(new MultipleChoiceMatrix('program_usage_time_of_the_day',Lang::getString('questionnaireProgramUsageTimOfTheDay',$lang),true,
        [
            'weekdays'=>[
                'label'=>Lang::getString('questionnaireProgramUsageTimOfTheDayWeekdays',$lang),
                'code'=>'weekdays',
                'values'=>[
                   'morning'=>Lang::getString('questionnaireProgramUsageTimOfTheDayMorning',$lang),
                   'beforenoon'=>Lang::getString('questionnaireProgramUsageTimOfTheDayBeforenoon',$lang),
                   'afternoon'=>Lang::getString('questionnaireProgramUsageTimOfTheDayAfternoon',$lang)
                ]
            ],
            'weekends'=>[
                'label'=>Lang::getString('questionnaireProgramUsageTimOfTheDayWeekends',$lang),
                'code'=>'weekends',
                'values'=>[
                   'morning'=>Lang::getString('questionnaireProgramUsageTimOfTheDayMorning',$lang),
                   'beforenoon'=>Lang::getString('questionnaireProgramUsageTimOfTheDayBeforenoon',$lang),
                   'afternoon'=>Lang::getString('questionnaireProgramUsageTimOfTheDayAfternoon',$lang)
                ]
            ]
        ]
    ));
    $questionnaire->addQuestion(new SingleChoice('recommendation_to_try',Lang::getString('questionnaireRecommendationToTry',$lang),true,
    [
        'y'=>Lang::getString('questionnaireYes',$lang),
        'n'=>Lang::getString('questionnaireNo',$lang)
    ]
    ));
    $questionnaire->addQuestion(new LongAnswer('recommendation_to_try_desc',Lang::getString('questionnaireRecommendationToTryDesc',$lang),true));
    $questionnaire->addQuestion(new ShortAnswer('math_knowledge_by_mark',Lang::getString('questionnaireMathKnowledgeByMark',$lang),true));
    $questionnaire->addQuestion(new SingleChoice('math_knowledge_after_program_usage',Lang::getString('questionnaireMathKnowledgeAfterProgramUsage',$lang),true,
    [
        'y'=>Lang::getString('questionnaireYes',$lang),
        'n'=>Lang::getString('questionnaireNo',$lang)
    ]
    ));
    $questionnaire->addQuestion(new SingleChoice('gender',Lang::getString('questionnaireGender',$lang),true,
    [
        'f'=>Lang::getString('questionnaireGenderFemale',$lang),
        'm'=>Lang::getString('questionnaireGenderMale',$lang)
    ]
    ));
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='apple-touch-icon' sizes='180x180' href='<?php echo Rootfolder::getPath().'/src/favicon/apple-touch-icon.png'?>'>
    <link rel='icon' type='image/png' sizes='32x32' href='<?php echo Rootfolder::getPath().'/src/favicon/favicon-32x32.png'?>'>
    <link rel='icon' type='image/png' sizes='16x16' href='<?php echo Rootfolder::getPath().'/src/favicon/favicon-16x16.png'?>'>
    <link rel='manifest' href='<?php echo Rootfolder::getPath().'/src/favicon/site.webmanifest'?>'>
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
    <title><?php echo Lang::getString('questionnairePageTitle',$lang);?></title>
</head>
<body>

<?php 
if (isset($_SESSION) && isset($_SESSION['messages']) && !isset($_SESSION['messages']['questionnairemessage_success'])) {
    if(isset($_SESSION['messages']['questionnairemessage_error'])){
        echo "<div>".$_SESSION['messages']['questionnairemessage_success']."</div>\n";
    }
    echo $questionnaire->getHTML();
}
else if(isset($_SESSION) && isset($_SESSION['messages']) && isset($_SESSION['messages']['questionnairemessage_success'])) {
    echo "<div>".$_SESSION['messages']['questionnairemessage_success']."</div>\n";
}
else {
    echo "<div>".Lang::getString('questionnaireUnknownError',$lang)."</div>\n";
}
?>

<form action='<?php echo Rootfolder::getPath().'/index.php'; ?>' method='post'>
    <button id='back' name='client' type='submit'><?php echo Lang::getString('backToTheMainPage',$lang)?></button>
</form>
</body>
</html>