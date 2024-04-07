<?php
namespace core\lib\venndiagrams\venndiagramshapes;

use \GdImage;


class Rectangle extends Shape 
{
    private Line $ASide;
    private Line $BSide;
    private Line $CSide;
    private Line $DSide;

    public function __construct(Line $ASide,Line $BSide,Line $CSide,Line $DSide) {
        $this->ASide=$ASide;
        $this->BSide=$BSide;
        $this->CSide=$CSide;
        $this->DSide=$DSide;

    }

    /**
     * Get the value of ASide
     */ 
    public function getASide()
    {
        return $this->ASide;
    }

    /**
     * Set the value of ASide
     *
     * @return  void
     */ 
    public function setASide($ASide)
    {
        $this->ASide = $ASide;
    }

    /**
     * Get the value of BSide
     */ 
    public function getBSide()
    {
        return $this->BSide;
    }

    /**
     * Set the value of BSide
     *
     * @return  void
     */ 
    public function setBSide($BSide)
    {
        $this->BSide = $BSide;
    }

    /**
     * Get the value of CSide
     */ 
    public function getCSide()
    {
        return $this->CSide;
    }

    /**
     * Set the value of CSide
     *
     * @return  void
     */ 
    public function setCSide($CSide)
    {
        $this->CSide = $CSide;
    }

    /**
     * Get the value of DSide
     */ 
    public function getDSide()
    {
        return $this->DSide;
    }

    /**
     * Set the value of DSide
     *
     * @return  void
     */ 
    public function setDSide($DSide)
    {
        $this->DSide = $DSide;
    }

    public function isPointInside(Point $point){
        return $this->ASide->isPointOnTheRight($point)&&
               $this->DSide->isPointOnTheLeft($point)&&
               $this->BSide->isPointAbove($point)&&
               $this->CSide->isPointBelow($point);
    }

    public function draw(GdImage $image, $color)
    {
        
        $topLeftX=$this->ASide->getB()->getX();
        $topLefty=$this->ASide->getB()->getY();
        $bottomRightX=$this->BSide->getB()->getX();
        $bottomRightY=$this->BSide->getB()->getY();
        return imagerectangle($image,$topLeftX,$topLefty,$bottomRightX,$bottomRightY,$color);
    }

    
}