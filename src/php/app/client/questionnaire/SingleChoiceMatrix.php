<?php
namespace app\client\questionnaire;

class SingleChoiceMatrix extends Question
{
    public function __construct($name,$questionText,$required,$questionAnswers){
        parent::__construct($name,$questionText,$required,$questionAnswers);
    }
    public function getHtml()
    {
        $required=$this->required?"required":"";
        $html="<div>\n";
        $html.="<div>$this->questionText</div>\n";
        $html.="<table>\n";
        $html.="<tr>\n";
        $html.="<th></th>\n";
        $columns=array_values($this->questionAnswers[array_key_first($this->questionAnswers)]['values']);

        foreach ($columns as $value) {
           $html.="<th>$value</th>";
        }
        $html.="</tr>\n";

        foreach ($this->questionAnswers as $row) {
            $html.="<tr>\n";
            $html.="<td>$row[label]</td>\n";
            foreach ($row['values'] as $key => $value) {
                $html.="<td><input type=\"radio\" name=\"$this->name";
                $html.="_";
                $html.="$row[code][]\" value=\"$key\" $required /></td>\n";
            }
            $html.="</tr>\n";
        }
        $html.="</table>\n";
        $html.="</div>\n";
        return $html;
    }
}