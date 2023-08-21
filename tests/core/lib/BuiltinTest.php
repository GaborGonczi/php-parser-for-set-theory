<?php

use \PHPUnit\Framework\TestCase;
use \core\lib\Builtin;
class BuiltinTest extends TestCase
{
    private $builtin;
    private $reflectionObject;


    protected function setUp(): void
    {
        $this->builtin=new Builtin;
        $this->reflectionObject= new ReflectionClass($this->builtin);
    }
    protected function tearDown(): void
    {
        unset($this->reflectionObject);
        unset($this->builtin);
    }
    /**
    * @uses \core\lib\Builtin
    */
    public function testType()
    {
       
        $this->assertEquals("builtin", $this->reflectionObject->getConstant('TYPE'));
    }

    /**
    * @uses \core\lib\Builtin
    */
    public function testNames()
    {
        $this->assertEquals(["PointSetDiagram", "Venn", "add", "delete"], $this->reflectionObject->getConstant('NAMES'));
    }
} 