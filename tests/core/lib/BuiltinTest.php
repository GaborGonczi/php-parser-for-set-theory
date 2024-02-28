<?php

use \PHPUnit\Framework\TestCase;
use \app\server\classes\Env;

class BuiltinTest extends TestCase
{
    private $reflectionObject;


    protected function setUp(): void
    {
        (new Env(dirname(dirname(dirname(dirname(__FILE__)))).'/.env',true))->load();
        $this->reflectionObject= new ReflectionClass("\core\lib\Builtin");
    }
    protected function tearDown(): void
    {
        unset($this->reflectionObject);
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