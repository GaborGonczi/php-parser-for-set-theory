<?php
namespace core\lib\venndiagrams\venndiagramshapes;


use \GdImage;

class NumberContainer
{
    private Point $textPosition;
    private string $value;
    private int $length;
    private float $sizeInPoints;
    private float $sizeInPixels;
    private int $leftOffset;
    private int $rightOffset;
    private int $topOffset;
    private int $bottomOffset;
    private Rectangle $boundRect;

    public function __construct(Point $textPosition,string $value,float $size) {
        $this->textPosition = $textPosition;
        $this->value=$value;
        $this->length=strlen($this->value);
        $this->sizeInPoints=$size;
        $this->sizeInPixels=$this->pointsToPixel($this->sizeInPoints);
        $this->leftOffset=0; // align to left side
        $this->rightOffset=intval($this->sizeInPixels*$this->length);
        $this->topOffset=intval($this->sizeInPixels);
        $this->bottomOffset=intval($this->sizeInPixels);

        $this->boundRect=new Rectangle(
            new Line(
                new Point($this->textPosition->getX()-$this->leftOffset,$this->textPosition->getY()+$this->bottomOffset),//A
                new Point($this->textPosition->getX()-$this->leftOffset,$this->textPosition->getY()-$this->topOffset)//D

            ),
            new Line(
                new Point($this->textPosition->getX()-$this->leftOffset,$this->textPosition->getY()+$this->bottomOffset),//A
                new Point($this->textPosition->getX()+$this->rightOffset,$this->textPosition->getY()+$this->bottomOffset)//B

            ),
            new Line(
                new Point($this->textPosition->getX()-$this->leftOffset,$this->textPosition->getY()-$this->topOffset),//D
                new Point($this->textPosition->getX()+$this->rightOffset,$this->textPosition->getY()-$this->topOffset)//C

            ),
            new Line(
                new Point($this->textPosition->getX()+$this->rightOffset,$this->textPosition->getY()+$this->bottomOffset),///B
                new Point($this->textPosition->getX()+$this->rightOffset,$this->textPosition->getY()-$this->topOffset)//C

            ),
        );

    }

    private function pointsToPixel($points){
        //https://pixelsconverter.com/pt-to-px
        return  $points * ( 72 / 96 );
    }
    public function write(GdImage $image,$color,$font){
        $result=imagettftext(
            $image,
            $this->sizeInPoints,
            0,
            $this->textPosition->getX(),
            $this->textPosition->getY(),
            $color,
            $font,
            $this->value,

        );
        return is_array($result)?true:false; 
        //imagestring($image,5,$this->textPosition->getX(),$this->textPosition->getY(),$this->value,$color);
    }

    //https://developer.mozilla.org/en-US/docs/Games/Techniques/2D_collision_detection
    public function notCollidingWith(NumberContainer $other){
        return
            !$this->boundRect->isPointInside($other->boundRect->getASide()->getA())&&
            !$this->boundRect->isPointInside($other->boundRect->getASide()->getB())&&
            !$this->boundRect->isPointInside($other->boundRect->getBSide()->getA())&& 
            !$this->boundRect->isPointInside($other->boundRect->getBSide()->getB())&&
            !$this->boundRect->isPointInside($other->boundRect->getCSide()->getA())&&
            !$this->boundRect->isPointInside($other->boundRect->getCSide()->getB())&&
            !$this->boundRect->isPointInside($other->boundRect->getDSide()->getA())&& 
            !$this->boundRect->isPointInside($other->boundRect->getDSide()->getB())&&

            !$other->boundRect->isPointInside($this->boundRect->getASide()->getA())&&
            !$other->boundRect->isPointInside($this->boundRect->getASide()->getB())&&
            !$other->boundRect->isPointInside($this->boundRect->getBSide()->getA())&& 
            !$other->boundRect->isPointInside($this->boundRect->getBSide()->getB())&&
            !$other->boundRect->isPointInside($this->boundRect->getCSide()->getA())&&
            !$other->boundRect->isPointInside($this->boundRect->getCSide()->getB())&&
            !$other->boundRect->isPointInside($this->boundRect->getDSide()->getA())&& 
            !$other->boundRect->isPointInside($this->boundRect->getDSide()->getB())
            
        ;
    }



    /**
     * Get the value of boundRect
     */ 
    public function getBoundRect()
    {
        return $this->boundRect;
    }

    /**
     * Set the value of boundRect
     *
     * @return  self
     */ 
    public function setBoundRect($boundRect)
    {
        $this->boundRect = $boundRect;

        return $this;
    }
}
