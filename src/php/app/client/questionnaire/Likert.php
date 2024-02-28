<?php
namespace app\client\questionnaire;

class Likert extends Question
{

    private $lowLabel;
    private $highLabel;

    public function __construct($name,$questionText,$required,$questionAnswers,$lowLabel,$highLabel){
        parent::__construct($name,$questionText,$required,$questionAnswers);
        $this->lowLabel=$lowLabel;
        $this->highLabel=$highLabel;
    }
    public function getHtml()
    {
        $required=$this->required?"required":"";
        $html="<div>\n";
        $html.="<div>$this->questionText</div>\n";
        $html.="<span>$this->lowLabel</span>\n";

        foreach ($this->questionAnswers as $key => $value) {
            $html.="<label><input type=\"radio\" name=\"$this->name\" value=\"$key\" $required /> $value</label>\n";
        }

        $html.="<span>$this->highLabel</span>\n";
        $html.="</div>\n";
        return $html;
    }
}