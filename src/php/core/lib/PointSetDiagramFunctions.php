<?php
namespace core\lib;

class PointSetDiagramFunctions{
    private static $colorpalette;
    public static function isPoint($point) {
        return gettype($point)==="object"&& $point instanceof Point;
    }
    public static function isPointArray($points) {
        if(!Functions::isArray($points)) Functions::illegalArguments(__METHOD__);
        foreach ($points as $point) {
            if (!PointSetDiagramFunctions::isPoint($point)) Functions::illegalArguments(__METHOD__);
        }
        return true;  
    }
    public static function isPointSet($points) {
        if(!Functions::isSet($points)) Functions::illegalArguments(__METHOD__);
        foreach ($points as $point) {
            if (!PointSetDiagramFunctions::isPoint($point)) Functions::illegalArguments(__METHOD__);
        }
        return true;  
    }

    public static function createSetFromPointArray($points) {
        if (!PointSetDiagramFunctions::isPointArray($points)) Functions::illegalArguments(__METHOD__);

        $result = new Set([]);

        foreach ($points as $point) {
            $result->add($point);
        }
        return $result;
    }
    public static function PointSetDiagram($points,$options=new PointSetDiagramOptions()){
        if(!PointSetDiagramFunctions::isPointSet($points)) Functions::illegalArguments(__METHOD__);
        $computedParams=[
            "xfrom"=>$options->get_x_from(),
            "xto"=>$options->get_x_to(),
            "yfrom"=>$options->get_y_from(),
            "yto"=>$options->get_y_to(),
            "xscale"=>$options->get_x_scale(),
            "yscale"=>$options->get_y_scale(),
            "WIDTH"=>$options->get_width(),
            "HEIGHT"=>$options->get_height(),
            "line_gap_x"=>$options->get_line_gap_x(),
            "line_gap_y"=>$options->get_line_gap_y(),
            "half_line_gap_x"=>$options->get_half_line_gap_x(),
            "half_line_gap_y"=>$options->get_half_line_gap_y(),
            "line_count_x"=>$options->get_line_count_x(),
            "line_count_y"=>$options->get_line_count_y(),
            "x_axis_y_coord"=>$options->get_x_axis_y_coord(),
            "y_axis_x_coord"=>$options->get_y_axis_x_coord()
        ];
        $imageWidth=$computedParams["WIDTH"];
        $imageHeight=$computedParams["HEIGHT"];
        $image=imagecreate($imageWidth,$imageHeight);
        Functions::initializeColorPalette($image);
        PointSetDiagramFunctions::$colorpalette=Functions::getColorPalette();
        imagefill($image,0,0,PointSetDiagramFunctions::$colorpalette["white"]);
        PointSetDiagramFunctions::drawHorizontalAxes($imageHeight,$image,$computedParams);
        PointSetDiagramFunctions::drawVerticalAxes($imageWidth,$image,$computedParams);
        PointSetDiagramFunctions::drawPoints($points,$image,$computedParams);
        ob_start();
        imagepng($image);
        $buffer=ob_get_contents();
        ob_end_clean();
        return 'data:image/png;base64,' . base64_encode($buffer);
    }
    public static function drawHorizontalAxes($width, $image, $computedParams) {
        imagesetthickness($image, 1);
        imageline($image, 0, 264, $width, 264, PointSetDiagramFunctions::$colorpalette["black"]);

        for ($i = 1; $i < 22; $i += 1) {

            if ($i == 11) continue;
            imageline($image,$i*24,264-12,$i*24,264+12,PointSetDiagramFunctions::$colorpalette["black"]);
            imagesetthickness($image, 1);         
        }
    }
    public static function drawVerticalAxes($height, $image, $computedParams) {
        imagesetthickness($image, 1);
        imageline($image,264 , 0, 264, $height, PointSetDiagramFunctions::$colorpalette["black"]);

        for ($i = 1; $i < 22; $i += 1) {

            if ($i == 11) continue;
            imagesetthickness($image, 1);
            imageline($image, 264-12,$i*24,264+12,$i*24,PointSetDiagramFunctions::$colorpalette["black"]);         
        }
    }
    public static function drawPoints($points, $image, $computedParams) {
        imagesetthickness($image, 1);
        foreach ($points as $point) {
            ["x"=>$x,"y"=>$y] = PointSetDiagramFunctions::getCanvasCoordinates($point->getX(), $point->getY(), $computedParams);
            imagearc($image, $x, $y, 10, 10,0,360, PointSetDiagramFunctions::$colorpalette["black"]);
        }
    }
    public static function getCanvasCoordinates($pointx, $pointy, $computedParams) {
               
        $origo["x"] = 264;
        $origo["y"] = 264;

        $step_on_x_axes = 24;
        $step_on_y_axes = 24;
        
        $canvas_coordinates["x"] = $origo["x"] + $step_on_x_axes * $pointx;
        $canvas_coordinates["y"] = $origo["y"] + $step_on_y_axes * -$pointy;

        return $canvas_coordinates;
    }

}
    
