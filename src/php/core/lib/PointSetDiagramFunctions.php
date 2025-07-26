<?php
namespace core\lib;

use \InvalidArgumentException;
use core\lib\datastructures\Point;
use core\lib\datastructures\Set;
use utils\Rootfolder;


/**
* A class that provides functions for drawing and manipulating point sets and diagrams.
*
* @package core\lib
*/
class PointSetDiagramFunctions
{

    /**
    * An array of colors for the point set diagram.
    */
    private static $colorpalette;

    /**
    * Checks if a variable is a Point object.
    *
    * @param mixed $point The variable to check.
    * @return bool True if the variable is a Point object, false otherwise.
    */
    public static function isPoint($point)
    {
        return gettype($point) === "object" && $point instanceof Point;
    }

    /**
    * Checks if a variable is an array of Point objects.
    *
    * @param mixed $points The variable to check.
    * @return bool True if the variable is an array of Point objects, false otherwise.
    * @throws InvalidArgumentException If the variable is not an array or contains non-Point elements.
    */
    public static function isPointArray($points)
    {
        if (!Functions::isArray($points)) return false;
        foreach ($points as $point) {
            if (!PointSetDiagramFunctions::isPoint($point)) return false;
        }
        return true;
    }

    /**
    * Checks if an array of sets is a valid point set array.
    *
    * This function takes an array of sets as an argument and checks if it is a valid point set array, which is an array of point sets that can be used to draw a point set diagram.
    * The function uses the Functions class to check if the argument is an array of sets and to throw an exception if it is not.
    * The function also uses the PointSetDiagramFunctions class to check if each set in the array is a point set and to throw an exception if it is not.
    * The function returns true if the argument is a valid point set array, otherwise it does not return anything as it throws an exception.
    *
    * @param array $sets The array of sets to check for validity.
    * @return bool True if the argument is a valid point set array.
    * @throws InvalidArgumentException If the argument is not an array of sets or if any set in the array is not a point set.
    */
    public static function isPointSetArray($sets)
    {
        if (!Functions::isSetArray($sets)) return false;
        foreach ($sets as $set) {
            if (!PointSetDiagramFunctions::isPointSet($set)) return false;
        }
        return true;
    }

    /**
    * Checks if a variable is a Set object of Point objects.
    *
    * @param mixed $points The variable to check.
    * @return bool True if the variable is a Set object of Point objects, false otherwise.
    * @throws InvalidArgumentException If the variable is not a Set object or contains non-Point elements.
    */
    public static function isPointSet($points)
    {
        if (!Functions::isSet($points)) return false;
        foreach ($points as $point) {
            if (!PointSetDiagramFunctions::isPoint($point)) return false;
        }
        return true;
    }

    /**
    * Adds a Point element to a Set object of Point objects.
    *
    * @param Point $element The Point element to add.
    * @param Set $set The Set object of Point objects to add to.
    * @return bool True if the element was added successfully, false otherwise.
    * @throws InvalidArgumentException If the element or the set are not valid Point objects or Set objects of Point objects, respectively.
    */
    public static function addPointElement($element, $set)
    {
        if (!PointSetDiagramFunctions::isPointSet($set) || !PointSetDiagramFunctions::isPoint($element)) throw Functions::illegalArguments(__METHOD__);
        $oldSize = $set->size();
        return $set->has($element) || $set->add($element)->size() === $oldSize + 1;

    }

    /**
    * Deletes a Point element from a Set object of Point objects.
    *
    * @param Point $element The Point element to delete.
    * @param Set $set The Set object of Point objects to delete from.
    * @return bool True if the element was deleted successfully, false otherwise.
    * @throws InvalidArgumentException If the element or the set are not valid Point objects or Set objects of Point objects, respectively.
    */
    public static function deletePointElement($element, $set)
    {
        if (!PointSetDiagramFunctions::isPointSet($set) || !PointSetDiagramFunctions::isPoint($element)) throw  Functions::illegalArguments(__METHOD__);
        $oldSize = $set->size();
        return !$set->has($element) || $set->delete($element)->size() === $oldSize - 1;
    }

    /**
    * Creates a Set object of Point objects from an array of Point objects.
    *
    * @param array $points The array of Point objects to convert.
    * @return Set A new Set object of Point objects containing the elements of the array.
    * @throws InvalidArgumentException If the array is not an array of Point objects.
    */
    public static function createSetFromPointArray($points)
    {
        if (!PointSetDiagramFunctions::isPointArray($points))
            throw Functions::illegalArguments(__METHOD__);

        $result = new Set([]);

        foreach ($points as $point) {
            $result->add($point);
        }
        return $result;
    }

    /**
    * Creates a PNG image of a point set diagram from a Set object of Point objects and an optional configuration object.
    *
    * @param Set $points The Set object of Point objects to draw on the image.
    * @param PointSetDiagramOptions $options An optional configuration object for the image. If not provided, default values will be used.
    * See the class definition for more details on the available options and their default values. 
    * @return string A base64 encoded string representation of the PNG image data. This can be used as the source attribute for an HTML image element.
    * @throws InvalidArgumentException If the points are not a valid Set object of Point objects, or the options are not a valid PointSetDiagramOptions object.
    */
    public static function PointSetDiagram($points, $options = new PointSetDiagramOptions())
    {
        if (!PointSetDiagramFunctions::isPointSet($points))
            throw Functions::illegalArguments(__METHOD__);
       
        $imageWidth = $options->getWidth();
        $imageHeight = $options->getHeight();
        $image = imagecreate($imageWidth, $imageHeight);
        Functions::initializeColorPalette($image);
        PointSetDiagramFunctions::$colorpalette = Functions::getColorPalette();
        imagefill($image, 0, 0, PointSetDiagramFunctions::$colorpalette["white"]);
        PointSetDiagramFunctions::drawGrid($image,$options);
        PointSetDiagramFunctions::drawDividingLines($image,$options);
        PointSetDiagramFunctions::writeText($image,$options);
        PointSetDiagramFunctions::drawAxesDirectionTriangle($image,$options);
        PointSetDiagramFunctions::drawPoints($points,$image,$options);

        ob_start();
        imagepng($image);
        $buffer = ob_get_contents();
        ob_end_clean();

        $data='data:image/png;base64,' . base64_encode($buffer);
        $html='<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
    <img src="'.$data.'"/>
</body>
</html>';
        file_put_contents(Rootfolder::getPhysicalPath().'/images/image.html',$html);
        return getenv('BASEURL').'/images/image.html';
    }


    /**
    * Draws the points of a Set object of Point objects on an image resource.
    *
    * @param Set $points The Set object of Point objects to draw on the image.
    * @param \GdImage $image The image resource to draw on.
    * @param PointSetDiagramOptions $options An associative array of the computed parameters for the image, such as the scale, the gaps, the counts, etc. See the PointSetDiagram function for more details on the keys and values of this array.
    * @return void

    * @codeCoverageIgnore
    */
    private static function drawPoints($points, $image, $options)
    {
        imagesetthickness($image, 1);
        foreach ($points as $point) {
            ["x" => $x, "y" => $y] = PointSetDiagramFunctions::getCanvasCoordinates($point->getX(), $point->getY(), $options);
            imagefilledarc($image, $x, $y, 10, 10, 0, 360, PointSetDiagramFunctions::$colorpalette["black"],IMG_ARC_PIE);
        }
    }

    /**
    * Converts the coordinates of a point in the point set diagram to the coordinates of a pixel on the image resource.
    *
    * @param int $pointx The x-coordinate of the point in the point set diagram.
    * @param int $pointy The y-coordinate of the point in the point set diagram.
    * @param PointSetDiagramOptions $options An associative array of the computed parameters for the image, such as the scale, the gaps, the counts, etc. See the PointSetDiagram function for more details on the keys and values of this array.
    * @return array An associative array with two keys: "x" and "y", representing the x-coordinate and y-coordinate of the pixel on the image resource, respectively.
    */
    public static function getCanvasCoordinates($pointx, $pointy, $options)
    {
     
        $origo["x"] = $options->getYAxesXPosition();
        $origo["y"] = $options->getXAxesYPosition();;

        $stepOnxAxes = $options->getCellWidth();
        $stepOnyAxes =$options->getCellHeight();

        $canvasCoordinates["x"] = $origo["x"] + $stepOnxAxes * $pointx;
        $canvasCoordinates["y"] = $origo["y"] + $stepOnyAxes * -$pointy;

        return $canvasCoordinates;
    }

    private static function drawGrid($image,$options){

        for ($i = $options->getLoopStart()+$options->getLoopOffset(); $i <= $options->getLoopEnd()+$options->getLoopOffset(); $i++) {
     
            if($i==$options->getLoopCenter()){
                imagesetthickness($image,$options->getAxesThickness());
                
            }
            else {
                imagesetthickness($image,$options->getGridThickness());
            }
            imageline($image, $i * $options->getCellWidth(), $options->getImageTopY(), $i * $options->getCellWidth(), $options->getHeight(), PointSetDiagramFunctions::$colorpalette["black"]);
            imageline($image, $options->getImageLeftEdgeX(), $i * $options->getCellHeight(), $options->getWidth(), $i * $options->getCellHeight(), PointSetDiagramFunctions::$colorpalette["black"]);
        
        }
    }

    private static function writeText($image,$options){
        imagesetthickness($image, $options->getGridThickness());
       
        imagestring($image, 5, ($options->convertDirectedCoordinateToAbsoluteCoordinate(5,'-x')- 0.5) * $options->getCellWidth(), ($options->getYAxesAbsoluteXCoordinate()+0.5) * $options->getCellHeight(), strval(-5), PointSetDiagramFunctions::$colorpalette["black"]);
        imagestring($image, 5, ($options->getYAxesAbsoluteXCoordinate() - 1) * $options->getCellWidth(), ($options->convertDirectedCoordinateToAbsoluteCoordinate(5,'+y')) * $options->getCellHeight(), strval(5), PointSetDiagramFunctions::$colorpalette["black"]);
        
        imagestring($image, 5, ($options->convertDirectedCoordinateToAbsoluteCoordinate(5,'+x')) * $options->getCellWidth(),($options->getYAxesAbsoluteXCoordinate()+0.5)  * $options->getCellHeight(), strval(5), PointSetDiagramFunctions::$colorpalette["black"]);
        imagestring($image, 5, ($options->getYAxesAbsoluteXCoordinate() - 1) * $options->getCellWidth(), ($options->convertDirectedCoordinateToAbsoluteCoordinate(5,'-y')) * $options->getCellHeight(), strval(-5), PointSetDiagramFunctions::$colorpalette["black"]);

        imagestring($image, 5, ($options->getYAxesAbsoluteXCoordinate()  - 1) * $options->getCellWidth(), ($options->getXAxesAbsoluteYCoordinate()  - 1) * $options->getCellHeight(), strval($options->getXAxesAbsoluteYCoordinate() - ($options->getXAxesAbsoluteYCoordinate()  - 1)), PointSetDiagramFunctions::$colorpalette["black"]);

        imagestring($image, 5, ($options->getYAxesAbsoluteXCoordinate()  + 1) * $options->getCellWidth(),($options->getXAxesAbsoluteYCoordinate()  +0.5) * $options->getCellHeight(), strval($options->getYAxesAbsoluteXCoordinate() - ($options->getYAxesAbsoluteXCoordinate()  - 1)), PointSetDiagramFunctions::$colorpalette["black"]);

       
    }

    private static function drawDividingLines($image,$options){
        imagesetthickness($image, $options->getAxesThickness());
        imageline($image, $options->getCellWidth() * (($options->getYAxesAbsoluteXCoordinate()-1)+0.5) , $options->getCellHeight() * ($options->getXAxesAbsoluteYCoordinate()-5), $options->getCellWidth() * ($options->getYAxesAbsoluteXCoordinate()+0.5), $options->getCellHeight() * ($options->getXAxesAbsoluteYCoordinate()-5), PointSetDiagramFunctions::$colorpalette["black"]);
        imageline($image,$options->getCellHeight() * ($options->getXAxesAbsoluteYCoordinate()-5),$options->getCellWidth() * (($options->getYAxesAbsoluteXCoordinate()-1)+0.5),$options->getCellHeight() * ($options->getXAxesAbsoluteYCoordinate()-5), $options->getCellWidth() * ($options->getYAxesAbsoluteXCoordinate()+0.5), PointSetDiagramFunctions::$colorpalette["black"]);

        imageline($image, $options->getCellWidth() *(($options->getYAxesAbsoluteXCoordinate()-1)+0.5) , $options->getCellHeight() * ($options->getXAxesAbsoluteYCoordinate()+5), $options->getCellWidth() * ($options->getYAxesAbsoluteXCoordinate()+0.5), $options->getCellHeight() * ($options->getXAxesAbsoluteYCoordinate()+5), PointSetDiagramFunctions::$colorpalette["black"]);
        imageline($image, $options->getCellHeight() * ($options->getXAxesAbsoluteYCoordinate()+5), $options->getCellWidth() * (($options->getYAxesAbsoluteXCoordinate()-1)+0.5), $options->getCellHeight() *($options->getXAxesAbsoluteYCoordinate()+5), $options->getCellWidth() * ($options->getYAxesAbsoluteXCoordinate()+0.5), PointSetDiagramFunctions::$colorpalette["black"]);

        imageline($image, $options->getCellWidth() * (($options->getYAxesAbsoluteXCoordinate()-1)+0.5), $options->getCellHeight() *($options->getXAxesAbsoluteYCoordinate()  - 1), $options->getCellWidth() * ($options->getYAxesAbsoluteXCoordinate()+0.5), $options->getCellHeight() * ($options->getXAxesAbsoluteYCoordinate()  - 1), PointSetDiagramFunctions::$colorpalette["black"]);

        imageline($image, $options->getCellHeight() *($options->getXAxesAbsoluteYCoordinate()  + 1), $options->getCellWidth() * (($options->getYAxesAbsoluteXCoordinate()  - 1)+0.5), $options->getCellHeight() * ($options->getXAxesAbsoluteYCoordinate()  + 1), $options->getCellWidth() * ($options->getYAxesAbsoluteXCoordinate()+0.5), PointSetDiagramFunctions::$colorpalette["black"]);
        imagesetthickness($image, $options->getGridThickness());
    }

    private static function drawAxesDirectionTriangle($image, PointSetDiagramOptions $options){
        $rightArrow=array(
            $options->getWidth(),$options->getXAxesYPosition(),
            $options->getWidth()-$options->getCellWidth()/4,$options->getXAxesYPosition()-($options->getCellHeight()/2),
            $options->getWidth()-$options->getCellWidth()/4,$options->getXAxesYPosition()+($options->getCellHeight()/2)
        );
        $topArrow=array(
            $options->getYAxesXPosition(),$options->getImageTopY(),
            $options->getYAxesXPosition()-($options->getCellWidth()/2),$options->getImageTopY()+$options->getCellHeight()/4,
            $options->getYAxesXPosition()+($options->getCellWidth()/2),$options->getImageTopY()+$options->getCellHeight()/4
        );
        imagefilledpolygon($image,$rightArrow,PointSetDiagramFunctions::$colorpalette['black']);
        imagefilledpolygon($image,$topArrow,PointSetDiagramFunctions::$colorpalette['black']);
    }

}

