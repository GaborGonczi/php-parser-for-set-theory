<?php
namespace core;

class Regexp{
    private $pattern;

    public function __construct($pattern) {
        $this->pattern="/$pattern/";
    }

    public function test($str){
        return boolval(preg_match($this->pattern,$str));
    }
}