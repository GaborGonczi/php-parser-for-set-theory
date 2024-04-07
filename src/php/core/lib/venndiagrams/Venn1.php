<?php
namespace core\lib\venndiagrams;
use core\lib\datastructures\Set;
use \core\lib\Functions;
use core\lib\venndiagrams\venndiagramshapes\Circle;
use core\lib\venndiagrams\venndiagramshapes\NumberContainer;
use core\lib\venndiagrams\venndiagramshapes\Point;
use utils\Rootfolder;
use \GdImage;

//maximum 50-50 elem halmazonként ha a szám 1 vagy 2 jegyű;
class Venn1 extends VennBase
{

    private Circle $ASet;

    private Set $AOnly;
  

    public function __construct(Set $A,string $fontfile,float $textSizeInPt){
        parent::__construct($fontfile,$textSizeInPt);

        $this->AOnly=$A;

        $this->ASet=new Circle(
            new Point(
                $this->unit+3*$this->unit+intval(2.25*$this->unit),
                $this->unit+3*$this->unit+2.5*$this->unit
            ),
            (3.55)*$this->unit
        );          
    }
    
    public function draw(){
       return $this->ASet->draw($this->image,self::$colorpalette['black'])&&
       $this->drawNumbers($this->image,self::$colorpalette['black']);
    }

    private function isContainerOnlyInA(NumberContainer $container){
        return $this->ASet->isContainerInside($container);
    }
    protected function drawNumbers(GdImage $image,$color){
        imagesetthickness($image,5);
        $numContainersA=[];
        $leftMostPointXCoordinateInSetA=2.95;
        $rightMostPointXCoordinateInSetA=10.05;
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
        
    $allpos=array_merge($numContainersA);
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
