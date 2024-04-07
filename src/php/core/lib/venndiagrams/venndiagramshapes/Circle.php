<?php
namespace core\lib\venndiagrams\venndiagramshapes;

use \GdImage;


class Circle extends Shape 
{
    private $startAngle;
    private $endAngle;
    private float $radius;
    private Point $center;
    public function __construct(Point $center,float $radius) {
        $this->center=$center;
        $this->radius=$radius;
        $this->startAngle=0;
        $this->endAngle=360;

    }

    /**
     * Get the value of center
     */ 
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * Set the value of center
     *
     * @return  void
     */ 
    public function setCenter($center)
    {
        $this->center = $center;
    }

    /**
     * Get the value of radius
     */ 
    public function getRadius()
    {
        return $this->radius;
    }

    /**
     * Set the value of radius
     *
     * @return  void
     */ 
    public function setRadius($radius)
    {
        $this->radius = $radius;
    }


    public function isPointInside(Point $point){
        return pow($point->getX()-$this->center->getX(),2)+pow($point->getY()-$this->center->getY(),2)<=pow($this->radius,2);
    }

    public function isContainerInside(NumberContainer $container){
        return $this->isPointInside($container->getBoundRect()->getASide()->getA())&&
        $this->isPointInside($container->getBoundRect()->getASide()->getB())&&
        $this->isPointInside($container->getBoundRect()->getDSide()->getA())&&
        $this->isPointInside($container->getBoundRect()->getDSide()->getB());
    }
    public function isContainerNotInside(NumberContainer $container){
        return !$this->isPointInside($container->getBoundRect()->getASide()->getA())&&
        !$this->isPointInside($container->getBoundRect()->getASide()->getB())&&
        !$this->isPointInside($container->getBoundRect()->getDSide()->getA())&&
        !$this->isPointInside($container->getBoundRect()->getDSide()->getB());
    }

    public function draw(GdImage $image, $color) {
      return imagearc($image,$this->center->getX(),$this->center->getY(),2*$this->radius,2*$this->radius,$this->startAngle,$this->endAngle,$color);
    }

}