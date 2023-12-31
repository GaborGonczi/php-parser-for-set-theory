<?php

use \PHPUnit\Framework\TestCase;
use \core\lib\datastructures\Point;
use \core\lib\PointSetDiagramFunctions;
use \core\lib\PointSetDiagramOptions;
use \core\lib\datastructures\Set;

class PointSetDiagramFunctionsTest extends TestCase
{

    private $image;


    protected function setUp(): void
    {
        $this->image = imagecreate(100, 100);
    }


    protected function tearDown(): void
    {
        imagedestroy($this->image);
    }


    public static function pointProvider()
    {
        return [
            [new Point(1, 2), true],
            [new Point(-3, 4), true],
            [new Point(0, 0), true],
            ["not a point", false],
            [null, false],
            [123, false]
        ];
    }


    /**
     * @dataProvider pointProvider
     * @covers \core\lib\PointSetDiagramFunctions::isPoint
     */
    public function testIsPoint($point, $expected)
    {
        $this->assertEquals($expected, PointSetDiagramFunctions::isPoint($point));
    }


    public static function pointArrayProvider()
    {
        return [
            [[new Point(1, 2), new Point(-3, 4)], true],
            [[new Point(0, 0), "not a point"], false],
            ["not an array", false],
            [null, false]
        ];
    }


    /**
     * @dataProvider pointArrayProvider
     * @covers \core\lib\PointSetDiagramFunctions::isPointArray
     */
    public function testIsPointArray($points, $expected)
    {
        if ($expected) {
            $this->assertTrue(PointSetDiagramFunctions::isPointArray($points));
        } else {
            $this->expectException(InvalidArgumentException::class);
            PointSetDiagramFunctions::isPointArray($points);
        }
    }


    public static function pointSetProvider()
    {
        return [
            [new Set([new Point(1, 2), new Point(-3, 4)]), true],
            [new Set(["not a point",new Point(0, 0)]), false],
            ["not a set", false],
            [null, false]
        ];
    }


    /**
     * @dataProvider pointSetProvider
     * @covers \core\lib\PointSetDiagramFunctions::isPointSet
     */
    public function testIsPointSet($points, $expected)
    {
        if ($expected) {
            $this->assertTrue(PointSetDiagramFunctions::isPointSet($points));
        } else {
            $this->expectException(InvalidArgumentException::class);
            PointSetDiagramFunctions::isPointSet($points);
        }
    }

    
    /**
     * @covers \core\lib\PointSetDiagramFunctions::addPointElement
     * @uses \core\lib\datastructures\Set
     * @uses \core\lib\datastructures\Point
     */
    public function testAddPointElement()
    {

        $set = new Set([new Point(1, 2),new Point(3, 4)]);
        $this->assertTrue(PointSetDiagramFunctions::addPointElement(new Point(5,6), $set));
        $this->assertTrue($set->has(new Point(5,6)));


        $this->assertTrue(PointSetDiagramFunctions::addPointElement(new Point(3, 4), $set));
        $this->assertEquals(3, $set->size());


        $this->expectException(InvalidArgumentException::class);
        PointSetDiagramFunctions::addPointElement('a', 'b');
    }



    /**
     * @covers \core\lib\PointSetDiagramFunctions::deletePointElement
     * @uses \core\lib\datastructures\Set
     * @uses \core\lib\datastructures\Point
     */
    public function testDeletePointElement()
    {

        $set = new Set([new Point(1, 2),new Point(5, 6)]);
        $this->assertTrue(PointSetDiagramFunctions::deletePointElement(new Point(1, 2), $set));
        $this->assertFalse($set->has(new Point(1, 2)));


        $this->assertTrue(PointSetDiagramFunctions::deletePointElement(new Point(3, 4), $set));
        $this->assertEquals(1, $set->size());


        $this->expectException(InvalidArgumentException::class);
        PointSetDiagramFunctions::deletePointElement('a', 'b');

    }



    public static function createSetFromPointArrayProvider()
    {
        return [
            [[new Point(1, 2), new Point(-3, 4)], new Set([new Point(1, 2), new Point(-3, 4)])],
            [[new Point(0, 0)], new Set([new Point(0, 0)])],
            ["not an array", null]
        ];
    }


    /**
     * @dataProvider createSetFromPointArrayProvider
     * @covers \core\lib\PointSetDiagramFunctions::createSetFromPointArray
     */
    public function testCreateSetFromPointArray($points, $expected)
    {
        if ($expected) {
            $this->assertEquals($expected, PointSetDiagramFunctions::createSetFromPointArray($points));
        } else {
            $this->expectException(InvalidArgumentException::class);
            PointSetDiagramFunctions::createSetFromPointArray($points);
        }
    }


    public static function getCanvasCoordinatesProvider()
    {
        return [
            [1, 2, ["x" => 288, "y" => 216]],
            [-3, 4, ["x" => 192, "y" => 168]],
            [0, 0, ["x" => 264, "y" => 264]]
        ];
    }


    /**
     * @dataProvider getCanvasCoordinatesProvider
     * @covers \core\lib\PointSetDiagramFunctions::getCanvasCoordinates
     */
    public function testGetCanvasCoordinates($pointx, $pointy, $expected)
    {
        $computedParams = [
            "xfrom" => -10,
            "xto" => 10,
            "yfrom" => -10,
            "yto" => 10,
            "xscale" => 1,
            "yscale" => 1,
            "WIDTH" => 528,
            "HEIGHT" => 528,
            "line_gap_x" => 24,
            "line_gap_y" => 24,
            "half_line_gap_x" => 12,
            "half_line_gap_y" => 12,
            "line_count_x" => 21,
            "line_count_y" => 21,
            "x_axis_y_coord" => 264,
            "y_axis_x_coord" => 264
        ];
        $this->assertEquals($expected, PointSetDiagramFunctions::getCanvasCoordinates($pointx, $pointy, $computedParams));
    }

    /**
     * @covers \core\lib\PointSetDiagramFunctions::pointSetDiagram
     * @uses \core\lib\datastructures\Set
     * @uses \core\lib\datastructures\Point
     * @uses \core\lib\PointSetDiagramOptions
     */
    public function testPointSetDiagram()
    {
        $points = new Set([new Point(1, 2), new Point(-3, 4), new Point(0, 0)]);
        $options = new PointSetDiagramOptions();


        $imageData = PointSetDiagramFunctions::pointSetDiagram($points, $options);
        $imageData = substr($imageData, strlen('data:image/png;base64,'));
        $imageData = base64_decode($imageData);

        $this->assertNotFalse(imagecreatefromstring($imageData));
        list($width, $height) = getimagesizefromstring($imageData);
        $this->assertEquals(504, $width);
        $this->assertEquals(504, $height);
    }
}