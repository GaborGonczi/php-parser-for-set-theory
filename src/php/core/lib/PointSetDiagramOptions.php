<?php
namespace core\lib;

class PointSetDiagramOptions {
    
    private $xfrom;
    private $xto;
    private $yfrom;
    private $yto;
    private $xscale;
    private $yscale;
    private $WIDTH;
    private $HEIGHT;
    private $line_gap_x;
    private $line_gap_y;
    private $half_line_gap_x;
    private $half_line_gap_y;
    private $line_count_x;
    private $line_count_y;
    private $x_axis_y_coord;
    private $y_axis_x_coord;
        
    public function __construct() {
        $this->xfrom = -10;
        $this->xto = 10;
        $this->yfrom = -10;
        $this->yto = 10;
        $this->xscale = 1;
        $this->yscale = 1;
        
        $this->computeParams();
    }
      
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
    
    public function get_x_from() {
        return $this->xfrom;
    }
    
    public function get_x_to() {
        return $this->xto;
    }
    
    public function get_x_scale() {
        return $this->xscale;
    }
    
    public function get_y_from() {
        return $this->yfrom;
    }
    
    public function get_y_to() {
        return $this->yto;
    }
    
    public function get_y_scale() {
        return $this->yscale;
    }
    
    public function get_width() {
        return $this->WIDTH;
    }
    
    public function get_height() {
        return $this->HEIGHT;
    }
    
    public function get_line_gap_x() {
        return $this->line_gap_x;
    }
    
    public function get_line_gap_y() {
        return $this->line_gap_y;
    }
    
    public function get_half_line_gap_x() {
        return $this->half_line_gap_x;
    }
    
    public function get_half_line_gap_y() {
        return $this->half_line_gap_y;
    }
    
    public function get_line_count_x() {
        return $this->line_count_x;
    }
    
    public function get_line_count_y() {
        return $this->line_count_y;
    }
    
    public function get_x_axis_y_coord() {
        return $this->x_axis_y_coord;
    }
    
    public function get_y_axis_x_coord() {
        return $this->y_axis_x_coord;
    }
}
    