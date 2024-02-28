<?php
namespace app\client\questionnaire;

class SingleChoice extends Question
{
    public function __construct($name,$questionText,$required,$questionAnswers){
        parent::__construct($name,$questionText,$required,$questionAnswers);
    }
    public function getHtml()
    {
        $required=$this->required?"required":"";
        $html="<div>\n";
        $html.="<div>$this->questionText</div>\n";

        foreach ($this->questionAnswers as $key => $value) {
            $html.="<div><label><input type=\"radio\" name=\"$this->name[]\" value=\"$key\" $required />$value</label></div>\n";
        }

        $html.="</div>\n";
        return $html;
    }
}