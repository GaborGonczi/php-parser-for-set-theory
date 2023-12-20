<?php
namespace core\lib;

use \InvalidArgumentException;

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
        if (!Functions::isArray($points))
            Functions::illegalArguments(__METHOD__);
        foreach ($points as $point) {
            if (!PointSetDiagramFunctions::isPoint($point))
                Functions::illegalArguments(__METHOD__);
        }
        return true;
    }

    public static function isPointSetArray($sets)
    {
        if (!Functions::isSetArray($sets))
            Functions::illegalArguments(__METHOD__);
        foreach ($sets as $set) {
            if (!PointSetDiagramFunctions::isPointSet($set))
                Functions::illegalArguments(__METHOD__);
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
        if (!Functions::isSet($points))
            Functions::illegalArguments(__METHOD__);
        foreach ($points as $point) {
            if (!PointSetDiagramFunctions::isPoint($point))
                Functions::illegalArguments(__METHOD__);
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
        if (!PointSetDiagramFunctions::isPointSet($set) || !PointSetDiagramFunctions::isPoint($element))
            return Functions::illegalArguments(__METHOD__);
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
        if (!PointSetDiagramFunctions::isPointSet($set) || !PointSetDiagramFunctions::isPoint($element))
            return Functions::illegalArguments(__METHOD__);
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
            Functions::illegalArguments(__METHOD__);

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
            Functions::illegalArguments(__METHOD__);
        $computedParams = [
            "xfrom" => $options->get_x_from(),
            "xto" => $options->get_x_to(),
            "yfrom" => $options->get_y_from(),
            "yto" => $options->get_y_to(),
            "xscale" => $options->get_x_scale(),
            "yscale" => $options->get_y_scale(),
            "WIDTH" => $options->get_width(),
            "HEIGHT" => $options->get_height(),
            "line_gap_x" => $options->get_line_gap_x(),
            "line_gap_y" => $options->get_line_gap_y(),
            "half_line_gap_x" => $options->get_half_line_gap_x(),
            "half_line_gap_y" => $options->get_half_line_gap_y(),
            "line_count_x" => $options->get_line_count_x(),
            "line_count_y" => $options->get_line_count_y(),
            "x_axis_y_coord" => $options->get_x_axis_y_coord(),
            "y_axis_x_coord" => $options->get_y_axis_x_coord()
        ];
        $imageWidth = $computedParams["WIDTH"];
        $imageHeight = $computedParams["HEIGHT"];
        $image = imagecreate($imageWidth, $imageHeight);
        Functions::initializeColorPalette($image);
        PointSetDiagramFunctions::$colorpalette = Functions::getColorPalette();
        imagefill($image, 0, 0, PointSetDiagramFunctions::$colorpalette["white"]);
        PointSetDiagramFunctions::drawHorizontalAxes($imageHeight, $image, $computedParams);
        PointSetDiagramFunctions::drawVerticalAxes($imageWidth, $image, $computedParams);
        PointSetDiagramFunctions::drawPoints($points, $image, $computedParams);
        ob_start();
        imagepng($image);
        $buffer = ob_get_contents();
        ob_end_clean();
        return 'data:image/png;base64,' . base64_encode($buffer);
    }


    /**
    * Draws the horizontal axis and the tick marks on an image resource.
    *
    * @param int $width The width of the image in pixels.
    * @param resource $image The image resource to draw on.
    * @param array $computedParams An associative array of the computed parameters for the image, such as the scale, the gaps, the counts, etc. See the PointSetDiagram function for more details on the keys and values of this array.
    * @return void

    * @codeCoverageIgnore
    */
    private static function drawHorizontalAxes($width, $image, $computedParams)
    {
        imagesetthickness($image, 1);
        imageline($image, 0, 264, $width, 264, PointSetDiagramFunctions::$colorpalette["black"]);

        for ($i = 1; $i < 22; $i += 1) {

            if ($i == 11){
                continue;
            }
            else if($i==6|| $i==16){
                imagestring($image,5, $i * 24, 264 + 12+8,strval($i-11),PointSetDiagramFunctions::$colorpalette["black"]);
            }
                
            imageline($image, $i * 24, 264 - 12, $i * 24, 264 + 12, PointSetDiagramFunctions::$colorpalette["black"]);
            imagesetthickness($image, 1);
        }
    }

    /**
    * Draws the vertical axis and the tick marks on an image resource.
    *
    * @param int $height The height of the image in pixels.
    * @param resource $image The image resource to draw on.
    * @param array $computedParams An associative array of the computed parameters for the image, such as the scale, the gaps, the counts, etc. See the PointSetDiagram function for more details on the keys and values of this array.
    * @return void

    * @codeCoverageIgnore
    */
    private static function drawVerticalAxes($height, $image, $computedParams)
    {
        imagesetthickness($image, 1);
        imageline($image, 264, 0, 264, $height, PointSetDiagramFunctions::$colorpalette["black"]);

        for ($i = 1; $i < 22; $i += 1) {

            if ($i == 11){
                continue;
            }
            else if($i==6|| $i==16){
                imagestring($image,5, 264 - 30, $i * 24+(15*((($i-11)*-1)/5)),strval(($i-11)*-1),PointSetDiagramFunctions::$colorpalette["black"]);
            }
            imagesetthickness($image, 1);
            imageline($image, 264 - 12, $i * 24, 264 + 12, $i * 24, PointSetDiagramFunctions::$colorpalette["black"]);
        }
    }

    /**
    * Draws the points of a Set object of Point objects on an image resource.
    *
    * @param Set $points The Set object of Point objects to draw on the image.
    * @param resource $image The image resource to draw on.
    * @param array $computedParams An associative array of the computed parameters for the image, such as the scale, the gaps, the counts, etc. See the PointSetDiagram function for more details on the keys and values of this array.
    * @return void

    * @codeCoverageIgnore
    */
    private static function drawPoints($points, $image, $computedParams)
    {
        imagesetthickness($image, 1);
        foreach ($points as $point) {
            ["x" => $x, "y" => $y] = PointSetDiagramFunctions::getCanvasCoordinates($point->getX(), $point->getY(), $computedParams);
            imagearc($image, $x, $y, 10, 10, 0, 360, PointSetDiagramFunctions::$colorpalette["black"]);
        }
    }

    /**
    * Converts the coordinates of a point in the point set diagram to the coordinates of a pixel on the image resource.
    *
    * @param int $pointx The x-coordinate of the point in the point set diagram.
    * @param int $pointy The y-coordinate of the point in the point set diagram.
    * @param array $computedParams An associative array of the computed parameters for the image, such as the scale, the gaps, the counts, etc. See the PointSetDiagram function for more details on the keys and values of this array.
    * @return array An associative array with two keys: "x" and "y", representing the x-coordinate and y-coordinate of the pixel on the image resource, respectively.
    */
    public static function getCanvasCoordinates($pointx, $pointy, $computedParams)
    {

        $origo["x"] = 264;
        $origo["y"] = 264;

        $step_on_x_axes = 24;
        $step_on_y_axes = 24;

        $canvas_coordinates["x"] = $origo["x"] + $step_on_x_axes * $pointx;
        $canvas_coordinates["y"] = $origo["y"] + $step_on_y_axes * -$pointy;

        return $canvas_coordinates;
    }

}

