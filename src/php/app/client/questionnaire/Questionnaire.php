<?php
namespace app\client\questionnaire;

use \utils\Rootfolder;

class Questionnaire
{
    private  $questions;
    private $title;
    private $description;

    public function __construct($title,$description,$questions=[])
    {
        $this->title=$title;
        $this->description=$description;
        $this->questions=$questions;
    }

    public function addQuestion(Question $question)
    {
        $this->questions[]=$question;
    }

    public function getHTML()
    {
        $html="";
        $html.=$this->header();
        $html.=$this->getTitle();
        $html.=$this->getDescription();
        foreach ($this->questions as $question) {
            $html.=$question->getHTML();
        }
        $html.=$this->footer();
        return $html;
    }

    private function header()
    {
        $sendTo=Rootfolder::getPath().'/src/php/app/server/page/backendentry.php?page=questionnaire';
        return "<div class=\"container\"><form action=\"$sendTo\"method=\"post\">\n";
        
    }

    private function getTitle()
    {
        return "<div><h1>$this->title</h1></div>\n";
    }

    private function getDescription()
    {
        return "<div><p>$this->description</p></div>\n";
    }

    private function footer()
    {
        return "<input type=\"submit\" value=\"Küldés\" />\n</form>\n</div>\n<script>\n".$this->checkValidity()."</script>\n";
    }

    private function checkValidity(){
        $allNames=[];
        $errormessage='A többszörös választásnál legalább egy válasz kiválasztása kötelező.\nA többszörös rács választásnál soronként legalább egy válasz kiválasztása kötelező.';
        $js="function checkValidity(e)\n{\n";
        foreach ($this->questions as $question) {
            if($question instanceof MultipleChoice){
                $name=$question->getFullName();
                $js.="      let $name = [...document.querySelectorAll('[name=\"$name\"]')].some(item=>{
                                return item.checked;
                             });\n";
                $allNames[]=$name;
            }
            else if ($question instanceof MultipleChoiceMatrix){
                $names=$question->getFullName();
                $arrsymbol="[]";
                foreach ($names as $name) {
                  
                    $js.="      let $name = [...document.querySelectorAll('[name=\"$name$arrsymbol\"]')].some(item=>{
                        return item.checked;
                     });\n";
                     $allNames[]=$name;
                }
            }
        }
        $isSubmitted=implode("&&",$allNames);
        $js.="      if(!($isSubmitted)){\n";
        $js.="        e.preventDefault();\n";
        $js.="        alert(\"$errormessage\")\n";
        $js.="      }\n";
        $js.="}\n";
        $js.="document.querySelector(\"form\").addEventListener(\"submit\",checkValidity)\n";
        return $js;
    }


}
