<?php
namespace app\client\questionnaire;

class LongAnswer extends Question
{
    public function __construct($name,$questionText,$required){
        parent::__construct($name,$questionText,$required);
    }
    public function getHtml()
    {
        $required=$this->required?"required":"";

        $html="<div>\n";
        $html.="<div>$this->questionText</div>\n";
        $html.="<div><textarea name=\"$this->name\" maxlength=\"1000\" $required ></textarea></div>\n";
        $html.="</div>\n";
        return $html;

    }
}

