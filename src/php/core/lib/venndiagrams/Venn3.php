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

class Venn3 extends VennBase
{
    private Circle $ASet;
    private Circle $BSet;
    private Circle $CSet;
    private Set $AOnly;
    private Set $BOnly;
    private Set $COnly;
    private Set $AAndB;
    private Set $AAndC;
    private Set $BAndC;
    private Set $AAndBAndC;

    public function __construct(Set $A,Set $B,Set $C, string $fontfile, float $textSizeInPt){
    
        parent::__construct($fontfile,$textSizeInPt);

        $this->AAndBAndC=Functions::intersection($A,$B,$C);

        $this->AAndB=Functions::difference(Functions::intersection($A,$B),$this->AAndBAndC);
        $this->AAndC=Functions::difference(Functions::intersection($A,$C),$this->AAndBAndC);
        $this->BAndC=Functions::difference(Functions::intersection($B,$C),$this->AAndBAndC);

        $this->AOnly=Functions::difference($A,Functions::union($this->AAndB,$this->AAndC,$this->AAndBAndC));
        $this->BOnly=Functions::difference($B,Functions::union($this->AAndB,$this->BAndC,$this->AAndBAndC));
        $this->COnly=Functions::difference($C,Functions::union($this->AAndC,$this->BAndC,$this->AAndBAndC));




        $this->ASet=new Circle(
            new Point(
                $this->unit+3*$this->unit,
                $this->unit+3*$this->unit+0.5*$this->unit
            ),
            (3.55)*$this->unit
        );

        $this->BSet=new Circle(
            new Point(
                $this->unit+3*$this->unit+5*$this->unit,
                $this->unit+3*$this->unit+0.5*$this->unit
            ),
            (3.55)*$this->unit
        );
        
        $this->CSet=new Circle(
            new Point(
               ((4+9)/2)*$this->unit,
                intval($this->unit+3*$this->unit+4*$this->unit+0.5*$this->unit)
            ),
            (3.55)*$this->unit
        );
    }
    
    public function draw(){
       return $this->ASet->draw($this->image,self::$colorpalette['black'])&&
       $this->BSet->draw($this->image,self::$colorpalette['black'])&&
       $this->CSet->draw($this->image,self::$colorpalette['black'])&&
       $this->drawNumbers($this->image,self::$colorpalette['black']);
    }

    private function isContainerOnlyInA(NumberContainer $container){
        return $this->ASet->isContainerInside($container)&&
        $this->BSet->isContainerNotInside($container)&&
        $this->CSet->isContainerNotInside($container)
        ;
    }
    private function IsContainerOnlyInB(NumberContainer $container){
        return $this->ASet->isContainerNotInside($container)&&
        $this->BSet->isContainerInside($container)&&
        $this->CSet->isContainerNotInside($container);
    }
    private function IsContainerOnlyInC(NumberContainer $container){
        return $this->ASet->isContainerNotInside($container)&&
        $this->BSet->isContainerNotInside($container)&&
        $this->CSet->isContainerInside($container);
    }
    private function isContainerInABIntersection(NumberContainer $container){
        return $this->ASet->isContainerInside($container)&&
        $this->BSet->isContainerInside($container)&&
        $this->CSet->isContainerNotInside($container);
    }   
    private function isContainerInACIntersection(NumberContainer $container){
        return $this->ASet->isContainerInside($container)&&
        $this->BSet->isContainerNotInside($container)&&
        $this->CSet->isContainerInside($container);
    }  
    private function isContainerInBCIntersection(NumberContainer $container){
        return $this->ASet->isContainerNotInside($container)&&
        $this->BSet->isContainerInside($container)&&
        $this->CSet->isContainerInside($container);
    }   
    private function isContainerInABCIntersection(NumberContainer $container){
        return $this->ASet->isContainerInside($container)&&
        $this->BSet->isContainerInside($container)&&
        $this->CSet->isContainerInside($container);
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

        $numContainersC=[];
        $leftMostPointXCoordinateInSetC=2.95;
        $rightMostPointXCoordinateInSetC=7.1;
        $topMostPointYCoordinateInSetC=4.95;
        $bottomMostPointYCoordinateInSetC=12.05;
        foreach ($this->COnly as $value) {

            do {
                $success=true;
       
                $newpos=new Point(rand(intval($leftMostPointXCoordinateInSetC*$this->unit),intval($rightMostPointXCoordinateInSetC*$this->unit)),rand(intval($topMostPointYCoordinateInSetC*$this->unit),intval($bottomMostPointYCoordinateInSetC*$this->unit)));
                
                $possibleNewelem=new NumberContainer($newpos,$value,$this->textSizeInPt);
                

                
                if($this->IsContainerOnlyInC($possibleNewelem)){

                    foreach ($numContainersC as $container) {
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
            $numContainersC[]=$possibleNewelem;
            
    
        }
        
        $numContainersAB=[];
        $leftMostPointXCoordinateInSetAB=6.5;
        $rightMostPointXCoordinateInSetAB=7.55;     
        $topMostPointYCoordinateInSetAB=2.95;
        $bottomMostPointYCoordinateInSetAB=10.05;
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

        $numContainersAC=[];
        $leftMostPointXCoordinateInSetAC=5.775;
        $rightMostPointXCoordinateInSetAC=7.55;
        $topMostPointYCoordinateInSetAC=7.55;
        $bottomMostPointYCoordinateInSetAC=12.05;
        foreach ($this->AAndB as $value) {

            do {
                $success=true;

                $newpos=new Point(rand(intval($leftMostPointXCoordinateInSetAC*$this->unit),intval($rightMostPointXCoordinateInSetAC*$this->unit)),rand(intval($topMostPointYCoordinateInSetAC*$this->unit),intval($bottomMostPointYCoordinateInSetAC*$this->unit)));
                
                $possibleNewelem=new NumberContainer($newpos,$value,$this->textSizeInPt);
                
                if($this->isContainerInACIntersection($possibleNewelem)){

                    foreach ($numContainersAC as $container) {
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
            $numContainersAC[]=$possibleNewelem;
            
    
        }

 /*       $numContainersBC=[];
        $leftMostPointXCoordinateInSetBC=5.45;
        $rightMostPointXCoordinateInSetBC=7.55;
        $topMostPointYCoordinateInSetBC=4;
        $bottomMostPointYCoordinateInSetBC=9;
        foreach ($this->AAndB as $value) {

            do {
                $success=true;

                $newpos=new Point(rand(intval($leftMostPointXCoordinateInSetBC*$this->unit),intval($rightMostPointXCoordinateInSetBC*$this->unit)),rand(intval($topMostPointYCoordinateInSetBC*$this->unit),intval($bottomMostPointYCoordinateInSetBC*$this->unit)));
                
                $possibleNewelem=new NumberContainer($newpos,$value,$size);
                
                if($this->isContainerInBCIntersection($possibleNewelem)){

                    foreach ($numContainersBC as $container) {
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
            $numContainersBC[]=$possibleNewelem;
            
    
        }

        $numContainersABC=[];
        $leftMostPointXCoordinateInSetABC=5.45;
        $rightMostPointXCoordinateInSetABC=7.55;
        $topMostPointYCoordinateInSetABC=4;
        $bottomMostPointYCoordinateInSetABC=9;
        foreach ($this->AAndB as $value) {

            do {
                $success=true;

                $newpos=new Point(rand(intval($leftMostPointXCoordinateInSetABC*$this->unit),intval($rightMostPointXCoordinateInSetABC*$this->unit)),rand(intval($topMostPointYCoordinateInSetABC*$this->unit),intval($bottomMostPointYCoordinateInSetABC*$this->unit)));
                
                $possibleNewelem=new NumberContainer($newpos,$value,$size);
                
                if($this->isContainerInABCIntersection($possibleNewelem)){

                    foreach ($numContainersABC as $container) {
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
            $numContainersABC[]=$possibleNewelem;
            
    
        }*/


    $allpos=array_merge($numContainersA,$numContainersB,$numContainersC,$numContainersAB,$numContainersAC);
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
