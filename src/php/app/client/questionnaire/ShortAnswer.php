<?php
namespace app\client\questionnaire;

class ShortAnswer extends Question
{
    public function __construct($name,$questionText,$required){
        parent::__construct($name,$questionText,$required);
    }
    public function getHtml()
    {
        $required=$this->required?"required":"";

        $html="<div>\n";
        $html.="<div>$this->questionText</div>\n";
        $html.="<div><input type=\"text\" name=\"$this->name\" maxlength=\"255\" $required/></div>\n";
        $html.="</div>\n";
        return $html;    
    }
}
