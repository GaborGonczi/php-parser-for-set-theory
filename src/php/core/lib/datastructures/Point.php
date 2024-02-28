<?php
namespace core\lib\datastructures;

use \core\lib\exception\WrongArgumentException;
use \JsonSerializable;
use \ReflectionClass;
use \core\lib\Functions;

/**
* A class that represents a point in a two-dimensional plane.
*
* This class has two private properties: $x and $y, which store the coordinates of the point.
* It also provides methods to get the coordinates, compare two points for equality, and convert the point to a string representation.
*
* @namespace core\lib
*/
class Point implements JsonSerializable{

    /**
    * The x-coordinate of the point
    * @var int
    */
    private $x;

    /**
    * The y-coordinate of the point
    * @var int
    */
    private $y;

    /**
    * Constructs a new point from two numbers.
    *
    * @param int|float $x The x-coordinate of the point.
    * @param int|float $y The y-coordinate of the point.
    * @throws WrongArgumentException If the arguments are not valid numbers.
    */
    public function __construct($x,$y) 
    {
        if(!Functions::isNumber($x)||!Functions::isNumber($y)) throw Functions::illegalArguments(__METHOD__);
        if(!Functions::isWholeNumber($x)||!Functions::isWholeNumber($y))
        {
            $this->x=round($x,0,PHP_ROUND_HALF_UP);
            $this->y=round($y,0,PHP_ROUND_HALF_UP);
        }
        else{
            $this->x=$x;
            $this->y=$y;
        }
            
    }

    /**
    * Gets the x-coordinate of the point.
    *
    * @return int The x-coordinate of the point.
    */
    public function getX()
    {
        return $this->x;
    }

    /**
    * Gets the y-coordinate of the point.
    *
    * @return int The y-coordinate of the point.
    */
    public function getY()
    {
        return $this->y;
    }

    /**
    * Checks if two points are equal by comparing their coordinates.
    *
    * @param Point $point The other point to compare with.
    * @return bool True if the points have the same coordinates, false otherwise.
    */
    public function areEqual(Point $point):bool
    {
        return ($this->getX()===$point->getX())&&($this->getY()===$point->getY());
    }
    
    /**
    * Returns a string representation of the point in the format "[x,y]".
    *
    * @return string The string representation of the point.
    */
    public function __toString()
    {
        return "[". $this->x.",".$this->y."]";
    }

    /**
    * Gets the name of the point class.
    *
    * This function uses the ReflectionClass class to get the short name of the point class, which is the class name without the namespace.
    * The function returns the short name as a string.
    *
    * @return string The short name of the point class.
    */
    private function getName()
    {
        $ref=new ReflectionClass($this);
        return $ref->getShortName();

    }

    /**
    * Serializes the point object to JSON format.
    *
    * This function implements the JsonSerializable interface, which allows the point object to be serialized by the json_encode function.
    * The function returns an associative array with three keys: 'name', 'x', and 'y'. 
    * The values of these keys are the name, x-coordinate, and y-coordinate of the point object, respectively.
    *
    * @return array An associative array with three keys: 'name', 'x', and 'y'.
    */
    public function jsonSerialize():mixed
    {
        return ['name'=>$this->getName(),'x'=>$this->x,'y'=>$this->y];
    }
}