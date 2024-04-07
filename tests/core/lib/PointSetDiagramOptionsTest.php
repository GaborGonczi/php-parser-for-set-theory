<?php
use PHPUnit\Framework\TestCase;
use core\lib\PointSetDiagramOptions;

class PointSetDiagramOptionsTest extends TestCase
{
    private $options;

    protected function setUp(): void
    {
        $this->options = new PointSetDiagramOptions();
    }

    public function testGetCellWidth()
    {
        $this->assertEquals(30,$this->options->getCellWidth());
    }
    public function testSetCellWidth()
    {
        $this->options->setCellWidth(40);
        $this->assertEquals(40,$this->options->getCellWidth());
    }

    public function testGetCellHeight()
    {
        $this->assertEquals(30,$this->options->getCellHeight());
    }
    public function testSetCellHeight()
    {
        $this->options->setCellHeight(40);
        $this->assertEquals(40,$this->options->getCellHeight());
    }

    public function testGetXfrom()
    {
        $this->assertEquals(-10,$this->options->getXfrom());
    }
    public function testSetXfrom()
    {
        $this->options->setXfrom(-15);
        $this->assertEquals(-15,$this->options->getXfrom());
    }

    public function testGetXto()
    {
        $this->assertEquals(10,$this->options->getXto());
    }
    public function testSetXto()
    {
        $this->options->setXto(15);
        $this->assertEquals(15,$this->options->getXto());
    }

    public function testGetYfrom()
    {
        $this->assertEquals(-10,$this->options->getYfrom());
    }
    public function testSetYfrom()
    {
        $this->options->setYfrom(-15);
        $this->assertEquals(-15,$this->options->getYfrom());
    }

    public function testGetYto()
    {
        $this->assertEquals(10,$this->options->getYto());
    }
    public function testSetYto()
    {
        $this->options->setYto(15);
        $this->assertEquals(15,$this->options->getYto());
    }
    public function testGetXscale()
    {
        $this->assertEquals(1,$this->options->getXscale());
    }
    public function testSetXscale()
    {
        $this->options->setXscale(5);
        $this->assertEquals(5,$this->options->getXscale());
    }

    public function testGetYscale()
    {
        $this->assertEquals(1,$this->options->getYscale());
    }
    public function testSetYscale()
    {
        $this->options->setYscale(5);
        $this->assertEquals(5,$this->options->getYscale());
    }

    public function testGetAxesOffset()
    {
        $this->assertEquals(2,$this->options->getAxesOffset());
    }
    public function testSetAxesOffset()
    {
        $this->options->setAxesOffset(4);
        $this->assertEquals(4,$this->options->getAxesOffset());
    }
    public function testGetLoopOffset()
    {
        $this->assertEquals(1,$this->options->getLoopOffset());
    }
    public function testSetLoopOffset()
    {
        $this->options->setLoopOffset(4);
        $this->assertEquals(4,$this->options->getLoopOffset());
    }

    public function testGetLoopStart()
    {
        $this->assertEquals(0,$this->options->getLoopStart());
    }
    public function testSetLoopStart()
    {
        $this->options->setLoopStart(4);
        $this->assertEquals(4,$this->options->getLoopStart());
    }

    public function testGetLoopEnd()
    {
        $this->assertEquals(22,$this->options->getLoopEnd());
    }
    public function testSetLoopEnd()
    {
        $this->options->setLoopEnd(26);
        $this->assertEquals(26,$this->options->getLoopEnd());
    }
    public function testGetLoopCenter()
    {
        $this->assertEquals(11,$this->options->getLoopCenter());
    }
    public function testSetLoopCenter()
    {
        $this->options->setLoopCenter(15);
       $this->assertEquals(15,$this->options->getLoopCenter());
    }

    public function testGetGridThickness()
    {
        $this->assertEquals(1,$this->options->getGridThickness());
    }
    public function testSetGridThickness()
    {
        $this->options->setGridThickness(5);
        $this->assertEquals(5,$this->options->getGridThickness());
    }

    public function testGetAxesThickness()
    {
        $this->assertEquals(3,$this->options->getAxesThickness());
    }
    public function testSetAxesThickness()
    {
        $this->options->setAxesThickness(10);
        $this->assertEquals(10,$this->options->getAxesThickness());
    }
    public function testGetWidth()
    {
        $this->assertEquals(660,$this->options->getWidth());
    }
    public function testSetWidth()
    {
        $this->options->setWidth(1040);
        $this->assertEquals(1040,$this->options->getWidth());
    }

    public function testGetHeight()
    {
        $this->assertEquals(660,$this->options->getHeight());
    }
    public function testSetHeight()
    {
        $this->options->setHeight(1040);
        $this->assertEquals(1040,$this->options->getHeight());
    }

    public function testGetImageLeftEdgeX()
    {
        $this->assertEquals(0,$this->options->getImageLeftEdgeX());
    }
    public function testSetImageLeftEdgeX()
    {
        $this->options->setImageLeftEdgeX(160);
        $this->assertEquals(160,$this->options->getImageLeftEdgeX());
    }
    public function testGetImageTopY()
    {
        $this->assertEquals(0,$this->options->getImageTopY());
    }
    public function testSetImageTopY()
    {
        $this->options->setImageTopY(160);
        $this->assertEquals(160,$this->options->getImageTopY());
    }

    public function testGetXAxesAbsoluteYCoordinate()
    {
        $this->assertEquals(11,$this->options->getXAxesAbsoluteYCoordinate());
    }
    public function testSetXAxesAbsoluteYCoordinate()
    {
        $this->options->setXAxesAbsoluteYCoordinate(15);
        $this->assertEquals(15,$this->options->getXAxesAbsoluteYCoordinate());
    }

    public function testGetYAxesAbsoluteXCoordinate()
    {
        $this->assertEquals(11,$this->options->getYAxesAbsoluteXCoordinate());
    }
    public function testSetYAxesAbsoluteXCoordinate()
    {
        $this->options->setYAxesAbsoluteXCoordinate(15);
        $this->assertEquals(15,$this->options->getYAxesAbsoluteXCoordinate());
    }
    public function testGetXAxesYPosition()
    {
        $this->assertEquals(330,$this->options->getXAxesYPosition());
    }
    public function testSetXAxesYPosition()
    {
        $this->options->setXAxesYPosition(450);
        $this->assertEquals(450,$this->options->getXAxesYPosition());
    }

    public function testGetYAxesXPosition()
    {
        $this->assertEquals(330,$this->options->getYAxesXPosition());
    }
    public function testSetYAxesXPosition()
    {
        $this->options->setYAxesXPosition(450);
        $this->assertEquals(450,$this->options->getYAxesXPosition());
    }

    public function testConvertDirectedCoordinateToAbsoluteCoordinate()
    {
        // Assuming the default cellWidth and cellHeight are 30
        $this->assertEquals(12, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(1, '+x'));
        $this->assertEquals(10, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(1, '-x'));
        $this->assertEquals(10, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(1, '+y'));
        $this->assertEquals(12, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(1, '-y'));
        $this->assertEquals(16, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(5, '+x'));
        $this->assertEquals(6, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(5, '-x'));
        $this->assertEquals(6, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(5, '+y'));
        $this->assertEquals(16, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(5, '-y'));
        $this->assertEquals(21, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(10, '+x'));
        $this->assertEquals(1, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(10, '-x'));
        $this->assertEquals(1, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(10, '+y'));
        $this->assertEquals(21, $this->options->convertDirectedCoordinateToAbsoluteCoordinate(10, '-y'));
    }

}