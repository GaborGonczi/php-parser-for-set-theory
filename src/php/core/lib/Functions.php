<?php
// TODO: fix venn rendered result
// todo: Implement inclusion-exclusion priciple to calculate the cardinality of remaining sets in case 2 or 3 sets
namespace core\lib;

use \InvalidArgumentException;
use \core\Regexp;
class Functions
{
    private static $colorpalette=[];
    
    public static function illegalArguments($functionName){
        throw new InvalidArgumentException("Illegal arguments for $functionName");
    }

    public static function isNumber($element){
        return is_numeric($element);
    }

    public static function isString($literal){
        return is_string($literal);
    }

    public static function isArray($array){
        return is_array($array);
    }

    public static function isFunction($userDefinedFunction){
        return is_callable($userDefinedFunction);
    }

    public static function isWholeNumber($element){
        $regexp=new Regexp('^(0|[1-9][0-9]*)$');
        return Functions::isNumber($element)&&$regexp->test($element);
    }

    public static function isSet($set){
        return gettype($set)==="object"&&$set instanceof Set;
    }

    public static function createSetFromArray($array){
        if(!Functions::isArray($array)) return Functions::illegalArguments(__METHOD__);
        $result= new Set([]);
        foreach ($array as $value) {
            $result->add(floatval($value));
        }
        return $result;

    }

    public static function createSetFromFormula($start,$end,$formula){
        if(!Functions::isWholeNumber($start) || !Functions::isWholeNumber($end) || !Functions::isFunction($formula)) return Functions::illegalArguments(__METHOD__);
        $result=new Set([]);
        for ($i=$start; $i < $end+1 ; $i++) { 
            $result->add(floatval($formula($i)));
        }
        return $result;
    }

    public static function isEmpty($set){
        if(!Functions::isSet($set)) return Functions::illegalArguments(__METHOD__);
        return $set->size()===0;
    }

    public static function isElementOf($element,$set){
        if(!Functions::isSet($set) || !Functions::isWholeNumber($element)) return Functions::illegalArguments(__METHOD__);
        return $set->has($element);
    }

    public static function isNotElementOf($element,$set){
        if(!Functions::isSet($set) || !Functions::isWholeNumber($element)) return Functions::illegalArguments(__METHOD__);
        return !Functions::isElementOf($element,$set);
    }

    public static function difference($seta,$setb){
        if(!Functions::isSet($seta) || !Functions::isSet($setb)) return Functions::illegalArguments(__METHOD__);
        $result= new Set([]);
        foreach ($seta as $value) {
            if(!$setb->has($value)){
                $result->add($value);
            }
        }
        return $result;
    }

    public static function areEqual($seta,$setb){
        if(!Functions::isSet($seta) || !Functions::isSet($setb)) return Functions::illegalArguments(__METHOD__);
        return Functions::isEmpty(Functions::difference($seta,$setb)) && Functions::isEmpty(Functions::difference($setb,$seta));
    }

    public static function isSubsetOf($seta,$setb){
        if(!Functions::isSet($seta) || !Functions::isSet($setb)) return Functions::illegalArguments(__METHOD__);
        foreach ($seta as $value) {
            if(!$setb->has($value)){
              return false;
            }
        }
        return true;
    }

    public static function isRealSubsetOf($seta,$setb){
        if(!Functions::isSet($seta) || !Functions::isSet($setb)) return Functions::illegalArguments(__METHOD__);
        return Functions::isSubsetOf($seta,$setb) && !Functions::areEqual($seta,$setb);
    }

    public static function complement($set,$universe){
        if(!Functions::isSet($set) || !Functions::isSet($universe)) return Functions::illegalArguments(__METHOD__);
        return Functions::difference($universe,$set);
    }

    public static function union(...$sets){
        foreach ($sets as $set) {
           if(!Functions::isSet($set)) return Functions::illegalArguments(__METHOD__);
        }
        $result=new Set([]);
        foreach ($sets as $set) {
            foreach ($set as $value) {
               $result->add($value);
            }
        }
        return $result;
    }

    public static function intersection(...$sets){
        foreach ($sets as $set) {
            if(!Functions::isSet($set)) return Functions::illegalArguments(__METHOD__);
        }
        $result=[...$sets[0]];
        foreach ($sets as $set) {
           $result=array_intersect($result,[...$set]);
        }
        return new Set($result);
    }

    public static function cardinality($set){
        if(!Functions::isSet($set)) return Functions::illegalArguments(__METHOD__);
        return $set->size();
    }

    public static function addElement($element,$set){
        if(!Functions::isSet($set) || !Functions::isWholeNumber($element)) return Functions::illegalArguments(__METHOD__);
        $oldSize=$set->size();
        return $set->has($element) || $set->add($element)->size()===$oldSize+1;

    }

    public static function delElement($element,$set){
        if(!Functions::isSet($set) || !Functions::isWholeNumber($element)) return Functions::illegalArguments(__METHOD__);
        $oldSize=$set->size();
        return !$set->has($element) || $set->delete($element)->size()===$oldSize-1;
    }

    public static function venn(...$sets){
        if(count($sets)!==2&&count($sets)!==3){
            return Functions::illegalArguments(__METHOD__);
        }
        foreach ($sets as $set) {
            if(!Functions::isSet($set)) return Functions::illegalArguments(__METHOD__);
        }

        $image=imagecreate(500,500);
        Functions::initializeColorPalette($image);
        imagefill($image,0,0,Functions::$colorpalette["white"]);
    
        if(count($sets)==2){
            list($seta,$setb)=$sets;
            Functions::vennTwoSets($image,$seta,$setb);
            
        }
        else if(count($sets)==3){
            list($seta,$setb,$setc)=$sets;
            Functions::vennThreeSets($image,$seta,$setb,$setc);
        }
        
        ob_start();
        imagepng($image);
        $buffer=ob_get_contents();
        ob_end_clean();
        return 'data:image/png;base64,' . base64_encode($buffer);
    }

    /**
    * @codeCoverageIgnore
    */
    private static function vennTwoSets(&$image,$seta,$setb){
        $points=Functions::getVennPoints2();
        $setau=$points["visibleLines"]["setA"]["Au"];
        $setav=$points["visibleLines"]["setA"]["Av"];
        $setar=$points["visibleLines"]["setA"]["Ar"];
        $setad=$points["visibleLines"]["setA"]["Ad"];

        $setbu=$points["visibleLines"]["setB"]["Bu"];
        $setbv=$points["visibleLines"]["setB"]["Bv"];
        $setbr=$points["visibleLines"]["setB"]["Br"];
        $setbd=$points["visibleLines"]["setB"]["Bd"];

        $polygonA=$points["inSetAIfInThisPolygon"];
        $polygonB=$points["inSetBIfInThisPolygon"];
        $polygonAB=$points["inABIntersectionIfInThisPolygon"];

        imagearc($image,$setau,$setav,$setad,$setad,0,360,Functions::$colorpalette["black"]);
        imagearc($image,$setbu,$setbv,$setbd,$setbd,0,360,Functions::$colorpalette["black"]);

        $intersectionAB=Functions::intersection($seta,$setb);
        $setaonly=Functions::difference($seta,$intersectionAB);
        $setbonly=Functions::difference($setb,$intersectionAB);
        foreach ($setaonly as $number) {
            $coodinates=Functions::generateRandomPointInQuadrangle($polygonA["A2"]["x"],$polygonA["A2"]["y"],$polygonA["A3"]["x"],$polygonA["A3"]["y"],
            $polygonA["A4"]["x"],$polygonA["A4"]["y"],$polygonA["A5"]["x"],$polygonA["A5"]["y"]);
            imagestring($image,3,$coodinates["x"],$coodinates["y"],$number,Functions::$colorpalette["black"]);
        }
        foreach ($setbonly as $number) {
            $coodinates=Functions::generateRandomPointInQuadrangle($polygonB["B2"]["x"],$polygonB["B2"]["y"],$polygonB["B3"]["x"],$polygonB["B3"]["y"],
            $polygonB["B4"]["x"],$polygonB["B4"]["y"],$polygonB["B5"]["x"],$polygonB["B5"]["y"]);
            imagestring($image,3,$coodinates["x"],$coodinates["y"],$number,Functions::$colorpalette["black"]);
        }
        foreach ($intersectionAB as $number) {
            $coodinates=Functions::generateRandomPointInQuadrangle($polygonAB["C1"]["x"],$polygonAB["C1"]["y"],$polygonAB["B1"]["x"],$polygonAB["B1"]["y"],
            $polygonAB["C2"]["x"],$polygonAB["C2"]["y"],$polygonAB["A1"]["x"],$polygonAB["A1"]["y"]);
            imagestring($image,3,$coodinates["x"],$coodinates["y"],$number,Functions::$colorpalette["black"]);
        }
    }

    /**
    * @codeCoverageIgnore
    */
    private static function getVennPoints2(){
        $points=[
            "inSetAIfInThisPolygon"=>[
                //normal y coordinates must be multiply by -1
                /*"A1"=>[
                    "x"=>187.5, // x:187.5,y:250
                    'y'=>250
                ],*/
                "A2"=>[
                    "x"=>($A2x=round(187.5)), // x:187.5,y:-375
                    "y"=>($A2y=round(375))
                ],
                "A3"=>[
                    "x"=>($A3x=round(97.5)), // x:97.5,y:-336.75
                    "y"=>($A3y=round(336.75))
                ],
                "A4"=>[
                    "x"=>($A4x=round(97.5)),  //x:97.5,y:-163.25
                    "y"=>($A4y=round(163.25))
                ],
                "A5"=>[
                    "x"=>($A5x=round(187.5)), // x:187.5,y:-125
                    "y"=>($A5y=round(125))
                ]
                
            ],
            "inSetBIfInThisPolygon"=>[
                /*"B1"=>[
                    "x"=>312.5, // x:312.5,y:-250
                    "y"=>250
                ],*/
                "B2"=>[
                    "x"=>($B2x=round(202.5)), // x:202.5,y:-163.25
                    "y"=>($B2y=round(163.25)),
                ],
                "B3"=>[
                    "x"=>($B3x=round(402.5)), // x:402.5,y:-336.75
                    "y"=>($B3y=round(336.75)),
                ],
            
                "B4"=>[
                    "x"=>($B4x=round(314.5)), // x:314.5,y:-375
                    "y"=>($B4y=round(375))
                ],
                "B5"=>[
                    "x"=>($B5x=round(312.5)), // x:312.5,y:-125
                    "y"=>($B5y=round(125)),
                ]
                
            ],
            "inABIntersectionIfInThisPolygon"=>[
                "C1"=>[
                    "x"=>($C1x=round(250)), //x:250,y:-141.75 
                    "y"=>($C1y=round(141.75))
                ],
                "B1"=>[
                    "x"=>($B1x=round(312.5)), // x:312.5,y:-250
                    "y"=>($B1y=round(250))
                ],
                "C2"=>[
                    "x"=>($C2x=round(250)), // x:250,y:-358.25
                    "y"=>($C2y=round(358.25))
                ],
                "A1"=>[
                    "x"=>($A1x=round(187.5)), // x:187.5,y:250
                    'y'=>($A1y=round(250))
                ]
            ],
            "visibleLines"=>[
               "setA"=>[
                "Au"=>($Au=round(187.5)), //Kör egyenlet (x-u)^2+(y-v)^2=>(x - 187.5)² + (y + 250)² = 15625
                "Av"=>($Av=round(250)),
                "Ar"=>($Ar=round(125)),
                "Ad"=>($Ad=round($Ar*2))
               ],
               "setB"=>[
                "Bu"=>($Bu=round(312.5)), //Kör egyenlet (x-u)^2+(y-v)^2=>(x - 312.5)² + (y + 250)² = 15625
                "Bv"=>($Bv=round(250)),
                "Br"=>($Br=round(125)),
                "Bd"=>($Bd=round($Br*2))
               ]
            ]
        ];
        return $points;
    }

    /**
    * @codeCoverageIgnore
    */
    private static function generateRandomPointInQuadrangle($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4){
        do {           
            $x=Functions::random_float($x3,$x4);
            $y=Functions::random_float(Functions::random_float($y4,$y2),Functions::random_float($y3,$y1));
        } while (!Functions::isInsideQuadrangle($x, $y, $x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4));

         return ["x"=>round($x),"y"=>round($y)];
    }

    /**
     * @codeCoverageIgnore
    */
    private static function isInsideQuadrangle($x, $y, $x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4) {
        $coords = array($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4);
        $point = array($x, $y);
        $winding = 0;
            // 4. Loop through the four edges of the quadrangle
        for ($i = 0; $i < 4; $i++) {
            if((($point[1] > $coords[$i*2+1]) && ($point[1] <= $coords[($i+1)%4*2+1])) && ((($coords[($i+1)%4*2] - $coords[$i*2]) * ($point[1] - $coords[$i*2+1])) - (($point[0] - $coords[$i*2]) * ($coords[($i+1)%4*2+1] - $coords[$i*2+1])) > 0)){
                $crossing = 1;
            }
            else if(((($point[1] < $coords[$i*2+1]) && ($point[1] >= $coords[($i+1)%4*2+1])) && ((($coords[($i+1)%4*2] - $coords[$i*2]) * ($point[1] - $coords[$i*2+1])) - (($point[0] - $coords[$i*2]) * ($coords[($i+1)%4*2+1] - $coords[$i*2+1])) < 0))){
                $crossing = -1;
            }
            else{
                $crossing = 0;
            }
            $winding += $crossing;
        }
        return ($winding !=0);
    }
    
    /**
    * @codeCoverageIgnore
    */
    private static function vennThreeSets(&$image,$seta,$setb,$setc){
        //fix set A elements appearence
        $points=Functions::getVennPoints3();
        $setau=$points["visibleLines"]["setA"]["Au"];
        $setav=$points["visibleLines"]["setA"]["Av"];
        $setar=$points["visibleLines"]["setA"]["Ar"];
        $setad=$points["visibleLines"]["setA"]["Ad"];

        $setbu=$points["visibleLines"]["setB"]["Bu"];
        $setbv=$points["visibleLines"]["setB"]["Bv"];
        $setbr=$points["visibleLines"]["setB"]["Br"];
        $setbd=$points["visibleLines"]["setB"]["Bd"];

        $setcu=$points["visibleLines"]["setC"]["Cu"];
        $setcv=$points["visibleLines"]["setC"]["Cv"];
        $setcr=$points["visibleLines"]["setC"]["Cr"];
        $setcd=$points["visibleLines"]["setC"]["Cd"];

        $polygonA=$points["inSetAIfInThisPolygon"];
        $polygonB=$points["inSetBIfInThisPolygon"];
        $polygonC=$points["inSetCIfInThisPolygon"];
        $polygonAB=$points["inABIntersectionIfInThisPolygon"];
        $polygonAC=$points["inACIntersectionIfInThisPolygon"];
        $polygonBC=$points["inBCIntersectionIfInThisPolygon"];
        $polygonABC=$points["inABCIntersectionIfInThisPolygon"];

        imagearc($image,$setau,$setav,$setad,$setad,0,360,Functions::$colorpalette["black"]);
        imagearc($image,$setbu,$setbv,$setbd,$setbd,0,360,Functions::$colorpalette["black"]);
        imagearc($image,$setcu,$setcv,$setcd,$setcd,0,360,Functions::$colorpalette["black"]);

        
        $intersectionABC=Functions::intersection($seta,$setb,$setc);
        $intersectionAB=Functions::difference(Functions::intersection($seta,$setb),$intersectionABC);
        $intersectionAC=Functions::difference(Functions::intersection($seta,$setc),$intersectionABC);
        $intersectionBC=Functions::difference(Functions::intersection($setb,$setc),$intersectionABC);

        $setaonly=Functions::difference($seta,Functions::union($intersectionAB,$intersectionAC,$intersectionABC));
        $setbonly=Functions::difference($setb,Functions::union($intersectionAB,$intersectionBC,$intersectionABC));
        $setconly=Functions::difference($setc,Functions::union($intersectionAC,$intersectionBC,$intersectionABC));

       

        foreach ($setaonly as $number) {
            $coodinates=Functions::generateRandomPointInQuadrangle($polygonA["A1"]["x"],$polygonA["A1"]["y"],$polygonA["A2"]["x"],$polygonA["A2"]["y"],
            $polygonA["A3"]["x"],$polygonA["A3"]["y"],$polygonA["AB2"]["x"],$polygonA["AB2"]["y"]);
            imagestring($image,3,$coodinates["x"],$coodinates["y"],$number,Functions::$colorpalette["black"]);
        }
        foreach ($setbonly as $number) {
            $coodinates=Functions::generateRandomPointInQuadrangle($polygonB["B1"]["x"],$polygonB["B1"]["y"],$polygonB["B2"]["x"],$polygonB["B2"]["y"],$polygonB["AB3"]["x"],$polygonB["AB3"]["y"],
            $polygonB["B3"]["x"],$polygonB["B3"]["y"]);
            imagestring($image,3,$coodinates["x"],$coodinates["y"],$number,Functions::$colorpalette["black"]);
        }
        foreach ($setconly as $number) {
            $coodinates=Functions::generateRandomPointInQuadrangle($polygonC["C1"]["x"],$polygonC["C1"]["y"],$polygonC["C2"]["x"],$polygonC["C2"]["y"],
            $polygonC["C3"]["x"],$polygonC["C3"]["y"],$polygonC["C4"]["x"],$polygonC["C4"]["y"]);
            imagestring($image,3,$coodinates["x"],$coodinates["y"],$number,Functions::$colorpalette["black"]);
        }
        foreach ($intersectionAB as $number) {
            $coodinates=Functions::generateRandomPointInTriangle($polygonAB["AB1"]["x"],$polygonAB["AB1"]["y"],$polygonAB["AB2"]["x"],$polygonAB["AB2"]["y"],
            $polygonAB["AB3"]["x"],$polygonAB["AB3"]["y"]);
            imagestring($image,3,$coodinates["x"],$coodinates["y"],$number,Functions::$colorpalette["black"]);
        }
        foreach ($intersectionAC as $number) {
            $coodinates=Functions::generateRandomPointInTriangle($polygonAC["AC1"]["x"],$polygonAC["AC1"]["y"],$polygonAC["AC2"]["x"],$polygonAC["AC2"]["y"],
            $polygonAC["AC3"]["x"],$polygonAC["AC3"]["y"]);
            imagestring($image,3,$coodinates["x"],$coodinates["y"],$number,Functions::$colorpalette["black"]);
        }
        foreach ($intersectionBC as $number) {
            $coodinates=Functions::generateRandomPointInTriangle($polygonBC["BC1"]["x"],$polygonBC["BC1"]["y"],$polygonBC["BC2"]["x"],$polygonBC["BC2"]["y"],
            $polygonBC["BC3"]["x"],$polygonBC["BC3"]["y"]);
            imagestring($image,3,$coodinates["x"],$coodinates["y"],$number,Functions::$colorpalette["black"]);
        }
        foreach ($intersectionABC as $number) {
            $coodinates=Functions::generateRandomPointInTriangle($polygonABC["ABC1"]["x"],$polygonABC["ABC1"]["y"],$polygonABC["ABC2"]["x"],$polygonABC["ABC2"]["y"],
            $polygonABC["ABC3"]["x"],$polygonABC["ABC3"]["y"]);
            imagestring($image,3,$coodinates["x"],$coodinates["y"],$number,Functions::$colorpalette["black"]);
        }
    }

    /**
    * @codeCoverageIgnore
    */
    private static function getVennPoints3(){
        $points=[
            "inSetAIfInThisPolygon"=>[
                "A1"=>[
                    "x"=>($A1x=round(97.5)), // x:97.5,y:-336.75
                    "y"=>($A1y=round(336.75))
                ],
                "A2"=>[
                    "x"=>($A2x=round(97.5)), // x:97.5,y:-163.25
                    "y"=>($A2y=round(163.25))
                ],
                "A3"=>[
                    "x"=>($A3x=round(187.5)), // x:187.5,y:-125
                    "y"=>($A3y=round(125))
                ],
                "AB2"=>[
                    "x"=>($AB2x=round(188.5)), // x:188.5,y:-233.25
                    "y"=>($AB2y=round(233.25))
                ],
            ],
            "inSetBIfInThisPolygon"=>[
                "B1"=>[
                    "x"=>($B1x=round(402.5)), // x:402.5,y:-336.75
                    "y"=>($B1y=round(336.75))
                ],
                "B2"=>[
                    "x"=>($B2x=round(402.5)), // x:402.5,y:-163.25
                    "y"=>($B2y=round(163.25))
                ],
                "B3"=>[
                    "x"=>($B3x=round(312.5)), // x:312.5,y:-125
                    "y"=>($B3y=round(125))
                ],
                "AB3"=>[
                    "x"=>($AB3x=round(312.5)), // x:312.5,y:-233.25
                    "y"=>($AB3y=round(233.25))
                ]
            ],
            "inSetCIfInThisPolygon"=>[
                "C1"=>[
                    "x"=>($C1x=round(373.5)), // x:373.5,y:-375
                    "y"=>($C1y=round(375))
                ],
                "C2"=>[
                    "x"=>($C2x=round(312.5)),// x:312.5,y:-466.5
                    "y"=>($C2y=round(466.5))
                ],
                "C3"=>[
                    "x"=>($C3x=round(187.5)), // x:187.5,y:-466.5
                    "y"=>($C3y=round(466.5))
                ],
                "C4"=>[
                    "x"=>($C4x=round(126.5)), // x:126.5,y:-375
                    "y"=>($C4y=round(375))
                ]
            ],
            "inABIntersectionIfInThisPolygon"=>[
                "AB1"=>[
                    "x"=>($AB1x=round(250)), // x:250,y:-141.5
                    "y"=>($AB1y=round(141.5))
                ],
                "AB2"=>[
                    "x"=>($AB2x=round(188.5)), // x:188.5,y:-233.25
                    "y"=>($AB2y=round(233.25))
                ],
                "AB3"=>[
                    "x"=>($AB3x=round(312.5)), // x:312.5,y:-233.25
                    "y"=>($AB3y=round(233.25))
                ]
            ],
            "inACIntersectionIfInThisPolygon"=>[
                "AC1"=>[
                    "x"=>($AC1x=round(173.5)), // x:173.5,y:-259.25
                    "y"=>($AC1y=round(259.25)),
                ],
                "AC2"=>[
                    "x"=>($AC2x=round(125)), // x:125,y:-358.25
                    "y"=>($AC2y=round(358.25)),
                ],
                "AC3"=>[
                    "x"=>($AC3x=round(235)), // x:235,y:-365.5
                    "y"=>($AC3y=round(365.5)),
                ]
            ],
            "inBCIntersectionIfInThisPolygon"=>[
                "BC1"=>[
                    "x"=>($BC1x=round(326.5)),  // x:326.5,y:-259.25
                    "y"=>($BC1y=round(259.25)),
                ],
                "BC2"=>[
                    "x"=>($BC2x=round(265)), // x:265,y:-365.5
                    "y"=>($BC2y=round(365.5)),
                ],
                "BC3"=>[
                    "x"=>($BC3x=round(375)), // x:375,y:-358.25
                    "y"=>($BC3y=round(358.25)),
                ]
            ],
            "inABCIntersectionIfInThisPolygon"=>[
                "ABC1"=>[
                    "x"=>($ABC1x=round(187.5)), // x:187.5,y:-250
                    "y"=>($ABC1y=round(250)),
                ],
                "ABC2"=>[
                    "x"=>($ABC2x=round(250)), // x:250,y:-358.25
                    "y"=>($ABC2y=round(358.25)),
                ],
                "ABC3"=>[
                    "x"=>($ABC3x=round(312.5)), // x:312.5,y:-250
                    "y"=>($ABC3y=round(250)),
                ]
            ],
            "visibleLines"=>[
                "setA"=>[
                    "Au"=>($Au=round(187.5)), //Circle equation (x-u)^2+(y-v)^2=>(x - 187.5)² + (y + 250)² = 15625
                    "Av"=>($Av=round(250)),
                    "Ar"=>($Ar=round(125)),
                    "Ad"=>($Ad=round($Ar*2))
                   ],
                   "setB"=>[
                    "Bu"=>($Bu=round(312.5)), //Circle equation (x-u)^2+(y-v)^2=>(x - 312.5)² + (y + 250)² = 15625
                    "Bv"=>($Bv=round(250)),
                    "Br"=>($Br=round(125)),
                    "Bd"=>($Bd=round($Br*2))
                   ],
                   "setC"=>[
                    "Cu"=>($Cu=round(250)), //Circle equation (x-u)^2+(y-v)^2=>(x - 250)² + (y + 358.25)² = 15625
                    "Cv"=>($Cv=round(358.25)),
                    "Cr"=>($Cr=round(125)),
                    "Cd"=>($Cd=round($Cr*2))
                   ]
            ]
        ];
        return $points;
    }

    /**
    * @codeCoverageIgnore
    */
    private static function generateRandomPointInTriangle($x1, $y1, $x2, $y2, $x3, $y3){
        $r1 = Functions::random_float(0, 1);
        $r2 = Functions::random_float(0, 1);
        $x = (1 - sqrt($r1)) * $x1 + (sqrt($r1) * (1 - $r2)) * $x2 + (sqrt($r1) * $r2) * $x3;
        $y = (1 - sqrt($r1)) * $y1 + (sqrt($r1) * (1 - $r2)) * $y2 + (sqrt($r1) * $r2) * $y3;
        return ["x"=>round($x),"y"=>round($y)];
    }

    /**
    * @codeCoverageIgnore
    */
    function calculate_set_cardinality($input) {
 
        $a_union_b = $input ['a_union_b'];
        $a = $input ['a'];
        $b = $input ['b'];
        $a_intersection_b = $input ['a_intersection_b'];
        
    }
    
    /**
    * @codeCoverageIgnore
    */
    function calculate_set_cardinality3($input) {
        $a_union_b_union_c = $input ['a_union_b_union_c'];
        $a = $input ['a'];
        $b = $input ['b'];
        $c = $input ['c'];
        $a_intersection_b = $input ['a_intersection_b'];
        $a_intersection_c = $input ['a_intersection_c'];
        $b_intersection_c = $input ['b_intersection_c'];
        $a_intersection_b_intersection_c = $input ['a_intersection_b_intersection_c'];
        
    }
    
    /**
    * @codeCoverageIgnore
    */
    public static function initializeColorPalette($image){
        Functions::$colorpalette=[];
        Functions::$colorpalette["black"]=imagecolorallocate($image,0,0,0);
        Functions::$colorpalette["white"]=imagecolorallocate($image,255,255,255);
        Functions::$colorpalette["red"]=imagecolorallocate($image,255,0,0);
        Functions::$colorpalette["blue"]=imagecolorallocate($image,0,0,255);
        Functions::$colorpalette["yellow"]=imagecolorallocate($image,255,255,0);
        Functions::$colorpalette["purple"]=imagecolorallocate($image,128,0,128);
        Functions::$colorpalette["green"]=imagecolorallocate($image,0,255,0);
        Functions::$colorpalette["orange"]=imagecolorallocate($image,255,165,0);
    }

    /**
    * @codeCoverageIgnore
    */
    private static function random_float ($min,$max) {
        return ($min+lcg_value()*(abs($max-$min)));
    }
   
    /**
    * @codeCoverageIgnore
    */
    public static function getColorPalette(){
        return Functions::$colorpalette;
    }

}
