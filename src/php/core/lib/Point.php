<?php
namespace core\lib;
class Point{
    private $x;
    private $y;
    public function __construct($x,$y) {
      
        if(!Functions::isNumber($x)||!Functions::isNumber($y)) Functions::illegalArguments("Point class constructor");
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
    public function getX(){
        return $this->x;
    }
    public function getY(){
        return $this->y;
    }
    public function __toString(){
        return "[". $this->x.",".$this->y."]";
    }
}