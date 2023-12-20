<?php
namespace core\lib;

use \InvalidArgumentException;

/**
* A class that represents a point in a two-dimensional plane.
*
* This class has two private properties: $x and $y, which store the coordinates of the point.
* It also provides methods to get the coordinates, compare two points for equality, and convert the point to a string representation.
*
* @namespace core\lib
*/
class Point{

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
    * @throws InvalidArgumentException If the arguments are not valid numbers.
    */
    public function __construct($x,$y) 
    {
        if(!Functions::isNumber($x)||!Functions::isNumber($y)) Functions::illegalArguments(__METHOD__);
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
}