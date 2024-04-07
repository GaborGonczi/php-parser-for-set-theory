<?php
namespace app\server\classes\runnable;


use \app\server\classes\Database;
use app\server\classes\model\Questionaire;
use \app\server\classes\model\User;

use \DateTime;

use \utils\Rootfolder;

class QuestionnaireScript extends Runnable
{
    public function __construct(User $authedUser, Database $db)
    {
        parent::__construct($authedUser, $db);
    }

    public function run():string
    {
        if(!$this->isQuestionnaireExist()){
            $_POST["extend_program"]=htmlentities($_POST["extend_program"]);
            $_POST["math_knnowledge_by_mark"]=htmlentities( $_POST["math_knnowledge_by_mark"]);
            $_POST["recommendation_to_try_desc"]=htmlentities( $_POST["recommendation_to_try_desc"]);
            $_POST["average_daily_usage_in_minute"]=htmlentities($_POST["average_daily_usage_in_minute"]);

            $questionnaireModel= new Questionaire(null,$this->user->getId(),json_encode($_POST),date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), null);
            $id=$this->isQuestionnaireCreated($questionnaireModel->getAsAssociativeArray());
            if($id){
                $_SESSION['messages']['questionnairemessage_success']="Köszönöm, hogy a kérdőív kitöltésével támogattad a diplomamunkám elkészítését.";
                $this->redirectToQuestionnaire();
                return "";
            }
            $_SESSION['messages']['questionnairemessage_error']="Hiba történt a kérdőív mentése közben.";
            $this->redirectToQuestionnaire();
            return "";
        }
        else {
           $_SESSION['messages']['questionnairemessage_success']="Köszönöm, hogy a kérdőív kitöltésével támogattad a diplomamunkám elkészítését.";
           $this->redirectToQuestionnaire();
           return "";
        }
    }

    private function isQuestionnaireCreated($questionnaireArray) {
        return $this->db->insert('questionnaires', $questionnaireArray);
    }
    private function isQuestionnaireExist()
    {
        return $this->db->isExist('questionnaires', ['user_id' => $this->user->getId(), 'deleted_at' => null]);
    }
    private function redirectToQuestionnaire(){
        $location=Rootfolder::getPath().'/src/php/app/client/page/questionnaire.php';
        header("Location:$location");
        exit(0);
    }
}