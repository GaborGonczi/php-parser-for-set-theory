<?php

use \PHPUnit\Framework\TestCase;
use \core\lib\datastructures\Set;
use \app\server\classes\Env;

class SetTest extends TestCase
{

    private $set;


    protected function setUp(): void
    {
        (new Env(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/.env',true))->load();
        $this->set = new Set([1, 2, 3]);
    }
    protected function tearDown(): void
    {
        unset($this->set);
    }

    /**
    * @coverage \core\lib\Set::add
    */
    public function testAdd()
    {
        $this->set->add(4);
        $this->assertCount(4, $this->set);
        $this->assertTrue($this->set->has(4));
    }


    public static function addProvider()
    {
        return [

            [2, 3, [1.0, 2.0, 3.0]],
            
            [5, 4, [1.0, 2.0, 3.0, 5.0]],
            
            ['a', 3, [1.0, 2.0, 3.0]],
            
            [null, 3, [1.0, 2.0, 3.0]],
        ];
    }


    /**
    * @test add with data provider
    * @dataProvider addProvider
    * @coverage \core\lib\Set::add
    */
    public function add($element, $expectedSize, $expectedElements)
    {
        $this->set->add($element);
        $this->assertCount($expectedSize, $this->set);
        $this->assertSame($expectedElements, $this->set->values());
    }


    /**
    * @coverage \core\lib\Set::clear
    */
    public function testClear()
    {
        $this->set->clear();
        $this->assertCount(0, $this->set);
        $this->assertEmpty($this->set->values());
    }

    /**
    * @coverage \core\lib\Set::delete
    */
    public function testDelete()
    {
        $this->set->delete(2);
        $this->assertCount(2, $this->set);
        $this->assertFalse($this->set->has(2));
    }


    public static function deleteProvider()
    {
        return [

            [3, 2, [1.0, 2.0]],
            
            [4, 3, [1.0, 2.0, 3.0]],
            
            ['a', 3, [1.0, 2.0, 3.0]],
            
            [null, 3, [1.0, 2.0, 3.0]],
        ];
    }


    /**
    * @test delete with data provider
    * @dataProvider deleteProvider
    * @coverage \core\lib\Set::delete
    */
    public function delete($element, $expectedSize, $expectedElements)
    {
        $this->set->delete($element);
        $this->assertCount($expectedSize, $this->set);
        $this->assertSame($expectedElements, $this->set->values());
    }

    /**
    * @coverage \core\lib\Set::has
    */
    public function testHas()
    {
        $this->assertTrue($this->set->has(1));       
        $this->assertFalse($this->set->has(4));
    }


    /**
    * @coverage \core\lib\Set::size
    */
    public function testSize()
    {
        $this->assertEquals(3, $this->set->size());
    }


    /**
    * @coverage \core\lib\Set::getIterator
    */
    public function testGetIterator()
    {
        $iterator = $this->set->getIterator();
        $this->assertInstanceOf(Traversable::class, $iterator);
        $this->assertSame($this->set->values(), iterator_to_array($iterator));
    }

    /**
    * @coverage \core\lib\Set::values
    */
    public function testValues()
    {
        $this->assertIsArray($this->set->values());
        $this->assertCount(count(array_unique($this->set->values())), $this->set);
    }


    /**
    * @coverage \core\lib\Set::__toString
    */
    public function testToString()
    {
        $this->assertIsString((string) $this->set);
        $this->assertEquals('{1,2,3}', (string) $this->set);
    }
}