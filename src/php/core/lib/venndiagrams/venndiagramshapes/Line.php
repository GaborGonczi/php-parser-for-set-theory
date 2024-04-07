<?php
namespace core\lib\venndiagrams\venndiagramshapes;

use \core\lib\venndiagrams\venndiagramshapes\Point as Vector;

class Line {

    private Point $A;
    private Point $B;
    private Vector $normalvector;
    public function __construct(Point $A,Point $B) {
        $this->A=$A;
        $this->B=$B;
        $this->normalvector=new Vector($this->B->getY()-$this->A->getY(),-1*($this->B->getX()-$this->A->getX()));


    }
    

    /**
     * Get the value of A
     * 
     * @return Point
     */ 
    public function getA()
    {
        return $this->A;
    }

    /**
     * Set the value of A
     *
     * @return  void
     */ 
    public function setA(Point $A)
    {
        $this->A = $A;
    }

    /**
     * Get the value of B
     * 
     * @return Point
     */ 
    public function getB()
    {
        return $this->B;
    }

    /**
     * Set the value of B
     *
     * @return  void
     */ 
    public function setB(Point $B)
    {
        $this->B = $B;
    }

    public function isPointAbove(Point $point){

        return ($this->normalvector->getX()* $point->getX()
        +$this->normalvector->getY()*$point->getY())-($this->normalvector->getX()* $this->A->getX()
        +$this->normalvector->getY()*$this->A->getY())>0;
       
    }
    public function isPointBelow(Point $point){
        return ($this->normalvector->getX()* $point->getX()
        +$this->normalvector->getY()*$point->getY())-($this->normalvector->getX()* $this->A->getX()
        +$this->normalvector->getY()*$this->A->getY())<0;
    }
    public function isPointOnTheRight(Point $point){
        return $this->normalvector->getY()==0&&$this->normalvector->getX()*$point->getX()-$this->normalvector->getX()*$this->A->getX() <0;
    }
    public function isPointOnTheLeft(Point $point){
        return $this->normalvector->getY()==0&&$this->normalvector->getX()*$point->getX()-$this->normalvector->getX()*$this->A->getX() >0;
    }
    public function getLength(){
        return sqrt(pow($this->B->getX()-$this->A->getX(),2)+pow($this->B->getY()-$this->A->getY(),2));
    }

}