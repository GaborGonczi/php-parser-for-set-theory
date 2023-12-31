<?php


use PHPUnit\Framework\TestCase;
use \core\lib\datastructures\Map;

class MapTest extends TestCase
{
    private $map;

    protected function setUp(): void
    {
        $this->map = new Map(["one" => 1, "two" => 2, "three" => 3]);
    }

    protected function tearDown(): void
    {
        unset($this->map);
    }

    /**
     * @coverage \core\lib\Map::add
     */
    public function testAdd()
    {
        $this->map->add("four", 4);
        $this->assertEquals(4, $this->map->size());
        $this->assertEquals(["one", "two", "three", "four"], $this->map->keys());
    }

    public static function addProvider()
    {
        return [
            ["five", 5, 4, ["one", "two", "three", "five"]],
            ["two", 4, 3, ["one", "two", "three"]],
            ["six", "six", 4, ["one", "two", "three", "six"]],
            [null, null, 3, ["one", "two", "three"]]
        ];
    }

    /**
     * @test add with data provider
     * @dataProvider addProvider
     * @coverage \core\lib\Map::add
     */
    public function add($key, $value, $expectedSize, $expectedKeys)
    {
        $this->map->add($key, $value);
        $this->assertEquals($expectedSize, $this->map->size());
        $this->assertEquals($expectedKeys, $this->map->keys());
    }

    /**
     * @coverage \core\lib\Map::clear
     */
    public function testClear()
    {
        $this->map->clear();
        $this->assertEquals(0, $this->map->size());
        $this->assertEquals([], $this->map->keys());
    }

    /**
     * @coverage \core\lib\Map::delete
     */
    public function testDelete()
    {
        $this->map->delete("two");
        $this->assertEquals(2, $this->map->size());
        $this->assertEquals(["one", "three"], $this->map->keys());
    }

    public static function deleteProvider()
    {
        return [
            ["three", 2, ["one", "two"]],
            ["four", 3, ["one", "two", "three"]],
            ["one", 2, ["two", "three"]],
            [null, 3, ["one", "two", "three"]]
        ];
    }

    /**
     * @test delete with data provider
     * @dataProvider deleteProvider
     * @coverage \core\lib\Map::delete
     */
    public function delete($key, $expectedSize, $expectedKeys)
    {
        $this->map->delete($key);
        $this->assertEquals($expectedSize, $this->map->size());
        $this->assertEquals($expectedKeys, $this->map->keys());
    }

    /**
     * @coverage \core\lib\Map::has
     */
    public function testHas()
    {
        $this->assertTrue($this->map->has("one"));
        $this->assertTrue($this->map->has("two"));
        $this->assertTrue($this->map->has("three"));
        $this->assertFalse($this->map->has("four"));
    }

    /**
     * @coverage \core\lib\Map::get
     */
    public function testGet()
    {

        $this->assertEquals(2, $this->map->get("two"));
        $this->assertNull($this->map->get("four"));
    }


    /**
     * @coverage \core\lib\Map::size
     */
    public function testSize()
    {
        $this->assertEquals(3, $this->map->size());
    }

    /**
     * @coverage \core\lib\Map::keys
     */
    public function testKeys()
    {
        $this->assertEquals(["one", "two", "three"], $this->map->keys());
    }

    /**
     * @coverage \core\lib\Map::values
     */
    public function testValues()
    {
        $this->assertEquals([1, 2, 3], $this->map->values());
    }

    /**
     * @coverage \core\lib\Map::getIterator
     */
    public function testGetIterator()
    {
        $iterator = $this->map->getIterator();
        $this->assertInstanceOf(Traversable::class, $iterator);
        $this->assertSame(iterator_to_array($this->map), iterator_to_array($iterator));
    }

    /**
     * @coverage \core\lib\Map::__toString
     */
    public function testToString()
    {
        $this->assertIsString((string) $this->map);
        $this->assertEquals("{one:1,two:2,three:3}", (string) $this->map);
    }

    /**
     * @coverage \core\lib\Map::JsonSerialize
     */
    public function testJsonSerialize(): void
    {
        $object = new Map(["a" => 1, "b" => 2, "c" => 3]);
        $json = $object->jsonSerialize();
        $this->assertIsArray($json);
        $this->assertCount(3, $json);
        $this->assertContains(["a" => 1], $json);
        $this->assertContains(["b" => 2], $json);
        $this->assertContains(["c" => 3], $json);
    }
}