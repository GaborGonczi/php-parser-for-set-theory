<?php
namespace core\parser\exception;

use \Exception;

class LexerException extends Exception
{
    public function __construct($message, $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    public function __toString()
    {
        return $this->message;
    }

}