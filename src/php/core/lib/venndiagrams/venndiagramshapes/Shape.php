<?php
namespace core\lib\venndiagrams\venndiagramshapes;


use core\lib\venndiagrams\venndiagramshapes\Point;

use \GdImage;

abstract class Shape 
{
    abstract public function isPointInside(Point $point);
    abstract public function draw(GdImage $image,$color);
}
