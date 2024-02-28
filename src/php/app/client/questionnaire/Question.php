<?php
namespace app\client\questionnaire;

abstract class Question
{
    protected $name;
    protected  $questionText;
    protected  $questionAnswers;
    protected  $required;

    public function __construct($name,$questionText,$required=true,$questionAnswers=[]) {
        $this->name=$name;
        $this->questionText=$questionText;
        $this->questionAnswers=$questionAnswers;
        $this->required=$required;

    }
    abstract public function getHTML();

    public function getFullName(){
       return $this->name;
    }
    
}
