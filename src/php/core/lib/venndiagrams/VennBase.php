<?php
namespace core\lib\venndiagrams;

use \core\lib\Functions;
use \utils\Rootfolder;

use \GdImage;



abstract class VennBase 
{
    protected $width;
    protected $height;
    protected $unit;
    protected $scalingFactor;
    protected $textSizeInPt;
    protected GdImage $image;
    protected string $fontfile;
    protected static $colorpalette;
    abstract public function draw();
    abstract protected function drawNumbers(GdImage $image,$color);

    public function __construct(string $fontfile,float $textSizeInPt){
        $this->unit=210;
        $this->scalingFactor=13;
        $this->textSizeInPt=$textSizeInPt;
        $this->width= $this->scalingFactor*$this->unit; // 210 unit 72-es betűméret= 96 50 unit 18-as betűméret
        $this->height= $this->scalingFactor*$this->unit;
        $this->image=$this->setUpImage();
        $this->fontfile=Rootfolder::getPhysicalPath().'/src/font/'.$fontfile;
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

    /**
     * Get the value of textSizeInPt
     */ 
    public function getTextSizeInPt()
    {
        return $this->textSizeInPt;
    }

    /**
     * Set the value of textSizeInPt
     *
     * @return  void
     */ 
    public function setTextSizeInPt($textSizeInPt)
    {
        $this->textSizeInPt = $textSizeInPt;
    }
}
