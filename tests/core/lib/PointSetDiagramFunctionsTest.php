<?php

use \PHPUnit\Framework\TestCase;
use \core\lib\datastructures\Point;
use \core\lib\PointSetDiagramFunctions;
use \core\lib\PointSetDiagramOptions;
use \core\lib\datastructures\Set;
use core\lib\exception\WrongArgumentException;
use \app\server\classes\Env;
class PointSetDiagramFunctionsTest extends TestCase
{

    private $image;


    protected function setUp(): void
    {
        (new Env(dirname(dirname(dirname(dirname(__FILE__)))).'/.env',true))->load();
        $_SERVER['SERVER_NAME']="localhost";
        $_SERVER['SERVER_PORT']=80;
        $_SERVER['REQUEST_URI']="";
        $_SERVER['DOCUMENT_ROOT']=getenv('BASEPATH');
        $this->image = imagecreate(100, 100);
    }

    protected function tearDown():void
    {
        $_SERVER['SERVER_NAME']="";
        $_SERVER['SERVER_PORT']="";
        $_SERVER['REQUEST_URI']="";
        $_SERVER['DOCUMENT_ROOT']="";
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
            $this->assertFalse(PointSetDiagramFunctions::isPointArray($points));
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
            $this->assertFalse(PointSetDiagramFunctions::isPointSet($points));
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


        $this->expectException(WrongArgumentException::class);
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


        $this->expectException(WrongArgumentException::class);
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
            $this->expectException(WrongArgumentException::class);
            PointSetDiagramFunctions::createSetFromPointArray($points);
        }
    }


    public static function getCanvasCoordinatesProvider()
    {
        return [
            [1, 2, ["x" => 360, "y" => 270]],
            [-3, 4, ["x" => 240, "y" => 210]],
            [0, 0, ["x" => 330, "y" => 330]]
        ];
    }


    /**
     * @dataProvider getCanvasCoordinatesProvider
     * @covers \core\lib\PointSetDiagramFunctions::getCanvasCoordinates
     */
    public function testGetCanvasCoordinates($pointx, $pointy, $expected)
    {
        $this->assertEquals($expected, PointSetDiagramFunctions::getCanvasCoordinates($pointx, $pointy, new PointSetDiagramOptions()));
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
        $imageData=str_replace(getenv('BASEURL'),getenv('BASEPATH'),$imageData);
        
        $this->assertNotFalse(file_exists($imageData));
        $content=file_get_contents($imageData);
        $start=strpos($content,"data:image/png");
        $end=strpos($content,'"',$start);
        $imageData=substr($content,$start,$end-$start);
        $imageData = substr($imageData, strlen('data:image/png;base64,'));
        $imageData = base64_decode($imageData);

        $this->assertNotFalse(imagecreatefromstring($imageData));
        list($width, $height) = getimagesizefromstring($imageData);
        $this->assertEquals(660, $width);
        $this->assertEquals(660, $height);
    }
}