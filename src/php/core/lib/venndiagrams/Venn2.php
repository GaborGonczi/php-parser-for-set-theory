<?php
namespace core\lib\venndiagrams;
use core\lib\datastructures\Set;
use \core\lib\Functions;
use core\lib\venndiagrams\venndiagramshapes\Circle;
use core\lib\venndiagrams\venndiagramshapes\NumberContainer;
use core\lib\venndiagrams\venndiagramshapes\Point;
use utils\Rootfolder;
use \GdImage;

//maximum 50-50 elem halmazonként ha a szám 1 vagy 2 jegyű; a metszetbe maximum 10 elem kerülhet

class Venn2 extends VennBase
{
    private Circle $ASet;
    private Circle $BSet;
    private Set $AOnly;
    private Set $BOnly;
    private Set $AAndB;

    public function __construct(Set $A,Set $B,string $fontfile,float $textSizeInPt){
       parent::__construct($fontfile,$textSizeInPt);

        $this->AOnly=Functions::difference($A,$B);
        $this->BOnly=Functions::difference($B,$A);
        $this->AAndB=Functions::intersection($A,$B);



        $this->ASet=new Circle(
            new Point(
                $this->unit+3*$this->unit,
                $this->unit+3*$this->unit+2.5*$this->unit
            ),
            (3.55)*$this->unit
        );

        $this->BSet=new Circle(
            new Point(
                $this->unit+3*$this->unit+5*$this->unit,
                $this->unit+3*$this->unit+2.5*$this->unit
            ),
            (3.55)*$this->unit
        );      
    }
    
    public function draw(){
       return $this->ASet->draw($this->image,self::$colorpalette['black'])&&
       $this->BSet->draw($this->image,self::$colorpalette['black'])&&
       $this->drawNumbers($this->image,self::$colorpalette['black']);
    }

    private function isContainerOnlyInA(NumberContainer $container){
        return $this->ASet->isContainerInside($container)&&
        $this->BSet->isContainerNotInside($container);
    }
    private function IsContainerOnlyInB(NumberContainer $container){
        return $this->ASet->isContainerNotInside($container)&&
        $this->BSet->isContainerInside($container);
    }

    private function isContainerInABIntersection(NumberContainer $container){
        return $this->ASet->isContainerInside($container)&&
        $this->BSet->isContainerInside($container);
    } 
    protected function drawNumbers(GdImage $image,$color){
        imagesetthickness($image,5);
        $numContainersA=[];
        $leftMostPointXCoordinateInSetA=0.45;
        $rightMostPointXCoordinateInSetA=7.55;
        $topMostPointYCoordinateInSetA=2.95;
        $bottomMostPointYCoordinateInSetA=10.05;
        
        foreach ($this->AOnly as $value) {

            do {

                $success=true;
                $newpos=new Point(rand(intval($leftMostPointXCoordinateInSetA*$this->unit),intval($rightMostPointXCoordinateInSetA*$this->unit)),rand(intval($topMostPointYCoordinateInSetA*$this->unit),intval($bottomMostPointYCoordinateInSetA*$this->unit)));
                $possibleNewelem=new NumberContainer($newpos,$value,$this->textSizeInPt);
                
                if($this->isContainerOnlyInA($possibleNewelem)){
                    
                    foreach ($numContainersA as $container) {
                        if(!$possibleNewelem->notCollidingWith($container)){
                            $success=false;
                            continue;
                        }
                    }
                    
                }
                else {
                    $success=false;
                }
            } while (!$success);
            $numContainersA[]=$possibleNewelem;
            
    
        }
        $numContainersB=[];
        $leftMostPointXCoordinateInSetB=6.5;
        $rightMostPointXCoordinateInSetB=11.5;
        $topMostPointYCoordinateInSetB=2.95;
        $bottomMostPointYCoordinateInSetB=10.05;
        foreach ($this->BOnly as $value) {

            do {
                $success=true;
       
                $newpos=new Point(rand(intval($leftMostPointXCoordinateInSetB*$this->unit),intval($rightMostPointXCoordinateInSetB*$this->unit)),rand(intval($topMostPointYCoordinateInSetB*$this->unit),intval($bottomMostPointYCoordinateInSetB*$this->unit)));
                
                $possibleNewelem=new NumberContainer($newpos,$value,$this->textSizeInPt);
                

                
                if($this->IsContainerOnlyInB($possibleNewelem)){
                    foreach ($numContainersB as $container) {
                        if(!$possibleNewelem->notCollidingWith($container)){
                            $success=false;
                            continue;
                        }
                    }
                    
                }
                else {
                    $success=false;
                }
            } while (!$success);
            $numContainersB[]=$possibleNewelem;
            
    
        }
        $numContainersAB=[];
        $leftMostPointXCoordinateInSetAB=5.45;
        $rightMostPointXCoordinateInSetAB=7.55;
        $topMostPointYCoordinateInSetAB=4;
        $bottomMostPointYCoordinateInSetAB=9;
        foreach ($this->AAndB as $value) {

            do {
                $success=true;

                $newpos=new Point(rand(intval($leftMostPointXCoordinateInSetAB*$this->unit),intval($rightMostPointXCoordinateInSetAB*$this->unit)),rand(intval($topMostPointYCoordinateInSetAB*$this->unit),intval($bottomMostPointYCoordinateInSetAB*$this->unit)));
                
                $possibleNewelem=new NumberContainer($newpos,$value,$this->textSizeInPt);
                
                if($this->isContainerInABIntersection($possibleNewelem)){

                    foreach ($numContainersAB as $container) {
                        if(!$possibleNewelem->notCollidingWith($container)){
                            $success=false;
                            continue;
                        }
                    }
                    
                }
                else {
                    $success=false;
                }
            } while (!$success);
            $numContainersAB[]=$possibleNewelem;
            
    
        }
    $allpos=array_merge($numContainersA,$numContainersB,$numContainersAB);
        foreach ($allpos as $value) {
           if(!$value->write($image,$color,$this->fontfile)){
                return false;
           }
        }
        return true;
    }
    private function setUpImage(){
        $image=imagecreate($this->width,$this->height);
        imagesetthickness($image,5);
        Functions::initializeColorPalette($image);
        self::$colorpalette=Functions::getColorPalette();
        imagefill($image,0,0,self::$colorpalette['white']);
        
       
        return $image;

    }
    public function getImage(){
        return $this->image;
    }
}
