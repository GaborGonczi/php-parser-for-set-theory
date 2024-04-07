<?php
namespace core\lib\venndiagrams\venndiagramshapes;

class Point {

    /**
    * The x-coordinate of the point
    * @var float
    */
    private $x;

    /**
    * The y-coordinate of the point
    * @var float
    */
    private $y;

    /**
    * Constructs a new point from two numbers.
    *
    * @param float $x The x-coordinate of the point.
    * @param float $y The y-coordinate of the point.
    */
    public function __construct($x,$y) 
    {
        $this->x=$x;
        $this->y=$y;
    }

    /**
    * Gets the x-coordinate of the point.
    *
    * @return float The x-coordinate of the point.
    */
    public function getX()
    {
        return $this->x;
    }

    /**
    * Gets the y-coordinate of the point.
    *
    * @return float The y-coordinate of the point.
    */
    public function getY()
    {
        return $this->y;
    }
   
}