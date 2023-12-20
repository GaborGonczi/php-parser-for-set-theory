<?php
namespace core\lib;

/**
* A class that represents the configuration options for a point set diagram image.
*
* @package core\lib
*/
class PointSetDiagramOptions {

    /**
    * The minimum value of the x-axis.
    */
    private $xfrom;

    /**
    * The maximum value of the x-axis.
    */
    private $xto;

    /**
    * The minimum value of the y-axis.
    */
    private $yfrom;

    /**
    * The maximum value of the y-axis.
    */
    private $yto;

    /**
    * The scale factor of the x-axis.
    */
    private $xscale;

    /**
    * The scale factor of the y-axis.
    */
    private $yscale;

    /**
    * The width of the image in pixels.
    */
    private $WIDTH;

    /**
    * The height of the image in pixels.
    */
    private $HEIGHT;

    /**
    * The gap between two consecutive tick marks on the x-axis in pixels.
    */
    private $line_gap_x;

    /**
    * The gap between two consecutive tick marks on the y-axis in pixels.
    */
    private $line_gap_y;

    /**
    *  Half of the gap between two consecutive tick marks on the x-axis in pixels.
    */
    private $half_line_gap_x;

    /**
    *  Half of the gap between two consecutive tick marks on the y-axis in pixels.
    */
    private $half_line_gap_y;

    /**
    * The number of tick marks on the x-axis.
    */
    private $line_count_x;

    /**
    * The number of tick marks on the y-axis.
    */
    private $line_count_y;

    /**
    * The y-coordinate of the x-axis on the image in pixels.
    */
    private $x_axis_y_coord;

    /**
    * The x-coordinate of the y-axis on the image in pixels.
    */
    private $y_axis_x_coord;
    
    /**
    * Constructs a new PointSetDiagramOptions object with default values.
    */
    public function __construct() {
        $this->xfrom = -10;
        $this->xto = 10;
        $this->yfrom = -10;
        $this->yto = 10;
        $this->xscale = 1;
        $this->yscale = 1;
        
        $this->computeParams();
    }
    
    /**
    * Computes and sets the parameters for the image based on the values of the axes and scales.
    *
    * @return void
    */
    private function computeParams() {
        $this->WIDTH = 504;
        $this->HEIGHT = 504;
        $this->line_gap_x =24;
        $this->line_gap_y = 24;
        $this->half_line_gap_x = 12;
        $this->half_line_gap_y =12;
        $this->line_count_x = 21;
        $this->line_count_y = 21;
        $this->x_axis_y_coord = 276;
        $this->y_axis_x_coord = 252;
    }
    
    /**
    * Gets the minimum value of the x-axis.
    *
    * @return int The minimum value of the x-axis.
    */
    public function get_x_from() {
        return $this->xfrom;
    }
    
    /**
    * Gets the maximum value of the x-axis.
    *
    * @return int The maximum value of the x-axis.
    */
    public function get_x_to() {
        return $this->xto;
    }
    
    /**
    * Gets the scale factor of the x-axis.
    *
    * @return int The scale factor of the x-axis.
    */
    public function get_x_scale() {
        return $this->xscale;
    }
    
    /**
    * Gets the minimum value of the y-axis.
    *
    * @return int The minimum value of the y-axis.
    */
    public function get_y_from() {
        return $this->yfrom;
    }
    
    /**
    * Gets the maximum value of the y-axis.
    *
    * @return int The maximum value of the y-axis.
    */
    public function get_y_to() {
        return $this->yto;
    }
    
    /**
    * Gets the scale factor of the y-axis.
    *
    * @return int The scale factor of the y-axis.
    */
    public function get_y_scale() {
        return $this->yscale;
    }
    
    /**
    * Gets the width of the image in pixels.
    *
    * @return int The width of the image in pixels.
    */
    public function get_width() {
        return $this->WIDTH;
    }
    
    /**
    * Gets the height of the image in pixels.
    *
    * @return int The height of the image in pixels.
    */
    public function get_height() {
        return $this->HEIGHT;
    }
    
    /**
    * Gets the gap between two consecutive tick marks on the x-axis in pixels.
    *
    * @return int The gap between two consecutive tick marks on the x-axis in pixels.
    */
    public function get_line_gap_x() {
        return $this->line_gap_x;
    }
    
    /**
    * Gets the gap between two consecutive tick marks on the y-axis in pixels.
    *
    * @return int The gap between two consecutive tick marks on the y-axis in pixels.
    */
    public function get_line_gap_y() {
        return $this->line_gap_y;
    }

    /**
    * Gets half of the gap between two consecutive tick marks on the x-axis in pixels.
    *
    * @return int Half of the gap between two consecutive tick marks on the x-axis in pixels.
    */
    public function get_half_line_gap_x() {
        return $this->half_line_gap_x;
    }
    
    /**
    * Gets half of the gap between two consecutive tick marks on the y-axis in pixels.
    *
    * @return int Half of the gap between two consecutive tick marks on the y-axis in pixels.
    */
    public function get_half_line_gap_y() {
        return $this->half_line_gap_y;
    }
    
    /**
    * Gets the number of tick marks on the x-axis.
    *
    * @return int The number of tick marks on the x-axis.
    */
    public function get_line_count_x() {
        return $this->line_count_x;
    }
    
    /**
    * Gets the number of tick marks on the y-axis.
    *
    * @return int The number of tick marks on the y-axis.
    */
    public function get_line_count_y() {
        return $this->line_count_y;
    }
    
    /**
    * Gets the y-coordinate of the x-axis on the image in pixels.
    *
    * @return int The y-coordinate of the x-axis on the image in pixels.
    */
    public function get_x_axis_y_coord() {
        return $this->x_axis_y_coord;
    }
    
    /**
    * Gets the x-coordinate of the y-axis on the image in pixels.
    *
    * @return int The x-coordinate of the y-axis on the image in pixels.
    */
    public function get_y_axis_x_coord() {
        return $this->y_axis_x_coord;
    }
}
    