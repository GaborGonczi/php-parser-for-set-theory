<?php
namespace core;

/**
* A class that represents a regular expression pattern and provides a method to test if a string matches it.
*
* @package core
*/
class Regexp{

    /**
    * A private property that stores the regular expression pattern as a string.
    *
    * @var string $pattern The regular expression pattern, with delimiters such as "/" or "#".
    */
    private $pattern;

    /**
    * Constructs a new Regexp object from a pattern string.
    *
    * @param string $pattern The pattern string to initialize the Regexp object with. The pattern should not include the delimiters, such as "/" or "#".
    */
    public function __construct($pattern) {
        $this->pattern="/$pattern/";
    }

    /**
    * Tests if a string matches the regular expression pattern of the Regexp object.
    *
    * @param string $str The string to test against the pattern.
    * @return bool True if the string matches the pattern, false otherwise.
    */
    public function test($str){
        return boolval(preg_match($this->pattern,$str));
    }
}