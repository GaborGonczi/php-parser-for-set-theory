<?php

use PHPUnit\Framework\TestCase;
use \core\Regexp;

class RegexpTest extends TestCase
{

   private $reflectionObject;

   protected function setUp():void
   {
        $this->reflectionObject=new ReflectionClass('\core\Regexp');
   }

   protected function tearDown():void
   {
        $this->reflectionObject=new ReflectionClass('\core\Regexp');
   }

    public function testConstructorWithEmptyString()
    {
        $withEmptyPattern=$this->reflectionObject->newInstance('');
        $patternProperty=$this->reflectionObject->getProperty('pattern');
        $this->assertSame('//', $patternProperty->getValue($withEmptyPattern));
       
    }

    public function testConstructorWithNonEmptyString()
    {
        $withEmptyPattern=$this->reflectionObject->newInstance('<=');
        $patternProperty=$this->reflectionObject->getProperty('pattern');
        $this->assertSame('/<=/', $patternProperty->getValue($withEmptyPattern));
       
    }

    /**
    * @dataProvider regexpProvider
    */
    public function testTest($pattern,$subject,$expected)
    {
        $regexp=new Regexp($pattern);
        $this->assertSame($expected,$regexp->test($subject));
    }

    public static function regexpProvider()
    {
        return [
            ['^(0|[1-9][0-9]*)$', '12',true],
            ['^(0|[1-9][0-9]*)$', '0',true],
            ['^(0|[1-9][0-9]*)$', '-1',false],
            ['^(0|[1-9][0-9]*)$', 'abc',false],
            ['^([_a-zA-Z][_a-zA-Z0-9]*)$','abc',true],
            ['^([_a-zA-Z][_a-zA-Z0-9]*)$','ABc',true],
            ['^([_a-zA-Z][_a-zA-Z0-9]*)$','_Abc1',true],
            ['^([_a-zA-Z][_a-zA-Z0-9]*)$','@Abc',false],
            ['^([_a-zA-Z][_a-zA-Z0-9]*)$','1Abc',false],
            [':=',':=',true],
            ['<=','<=',true],
            ['>=','>=',true],
            ['->','->',true],
        ];
    }
}