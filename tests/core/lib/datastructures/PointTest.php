<?php

use core\lib\exception\WrongArgumentException;
use \PHPUnit\Framework\TestCase;
use \core\lib\datastructures\Point;
use \app\server\classes\Env;

class PointTest extends TestCase
{

    public function setUp():void 
    {
        (new Env(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/.env',true))->load();
    }

    /**
     * @coverage \core\lib\Point
     */
    public function testConstructor()
    {

        $point = new Point(1, 2);
        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals(1, $point->getX());
        $this->assertEquals(2, $point->getY());


        $point = new Point(1.5, 2.4);
        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals(2, $point->getX());
        $this->assertEquals(2, $point->getY());


        $this->expectException(WrongArgumentException::class);
        $point = new Point('a', 'b');
    }


    /**
     * @coverage \core\lib\Point
     */
    public function testGetX()
    {

        $point = new Point(1, 2);
        $this->assertEquals(1, $point->getX());
    }

    /**
     * @coverage \core\lib\Point
     */
    public function testGetY()
    {

        $point = new Point(1, 2);
        $this->assertEquals(2, $point->getY());
    }

    /**
     * @coverage \core\lib\Point
     */
    public function testToString()
    {

        $point = new Point(1, 2);
        $this->assertEquals('[1,2]', (string) $point);
    }
}