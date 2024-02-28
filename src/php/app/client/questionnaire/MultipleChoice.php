<?php
namespace app\client\questionnaire;

class MultipleChoice extends Question
{
    public function __construct($name,$questionText,$required,$questionAnswers){
        parent::__construct($name,$questionText,$required,$questionAnswers);
    }
    public function getHtml()
    {
        $html="<div>\n";
        $html.="<div>$this->questionText</div>\n";
        foreach ($this->questionAnswers as $key => $value) {
            $html.="<div><label><input type=\"checkbox\" name=\"$this->name[]\" value=\"$key\"/>$value</label></div>\n";
        }

        $html.="</div>\n";
        return $html;    
    }
}
