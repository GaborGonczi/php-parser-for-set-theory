<?php
namespace core\lib\exception;

use \Exception;

/**
* A class that represents an exception thrown inside athe library which contains the processing methods.
*
* @package core\parser\exception
*/
class WrongArgumentException extends LibException
{
    /**
    * Constructs a new WrongArgumentException object with a given message, code, and previous exception.
    *
    * @param string $message The message of the exception.
    * @param int $val The code of the exception. Default is 0.
    * @param Exception $old The previous exception, if any. Default is null.
    */
    public function __construct($message, $val = 0, Exception $old = null)
    {
        parent::__construct($message, $val, $old);
    }

    /**
    * Returns a string representation of the exception, which is the same as the message.
    *
    * @return string The message of the exception.
    */
    public function __toString()
    {
        return $this->message;
    }

}