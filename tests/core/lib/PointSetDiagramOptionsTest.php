<?php

use \PHPUnit\Framework\TestCase;
use \core\lib\PointSetDiagramOptions;

class PointSetDiagramOptionsTest extends TestCase
{
    private $options;
    
    protected function setUp(): void
    {
        $this->options = new PointSetDiagramOptions();
    }
    
    /**
     * @covers \core\lib\PointSetDiagramOptions
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(PointSetDiagramOptions::class, $this->options);
        $this->assertEquals(-10, $this->options->get_x_from());
        $this->assertEquals(10, $this->options->get_x_to());
        $this->assertEquals(-10, $this->options->get_y_from());
        $this->assertEquals(10, $this->options->get_y_to());
        $this->assertEquals(1, $this->options->get_x_scale());
        $this->assertEquals(1, $this->options->get_y_scale());
        $this->assertEquals(504, $this->options->get_width());
        $this->assertEquals(504, $this->options->get_height());
        $this->assertEquals(24, $this->options->get_line_gap_x());
        $this->assertEquals(24, $this->options->get_line_gap_y());
        $this->assertEquals(12, $this->options->get_half_line_gap_x());
        $this->assertEquals(12, $this->options->get_half_line_gap_y());
        $this->assertEquals(21, $this->options->get_line_count_x());
        $this->assertEquals(21, $this->options->get_line_count_y());
        $this->assertEquals(276, $this->options->get_x_axis_y_coord());
        $this->assertEquals(252, $this->options->get_y_axis_x_coord());
    }
}