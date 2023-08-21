<?php

use \PHPUnit\Framework\TestCase;
use \core\lib\Functions;
use \core\lib\Set;
use core\Regexp;
class FunctionsTest extends TestCase
{

    /**
     * @covers \core\lib\Functions::isNumber
     */
    public function testIsNumber()
    {

        $this->assertTrue(Functions::isNumber(42));
        $this->assertTrue(Functions::isNumber(3.14));
        $this->assertTrue(Functions::isNumber('123'));


        $this->assertFalse(Functions::isNumber('abc'));
        $this->assertFalse(Functions::isNumber(null));
        $this->assertFalse(Functions::isNumber([]));
    }


    /**
     * @covers \core\lib\Functions::isString
     */
    public function testIsString()
    {

        $this->assertTrue(Functions::isString('hello'));
        $this->assertTrue(Functions::isString(''));


        $this->assertFalse(Functions::isString(42));
        $this->assertFalse(Functions::isString(true));
        $this->assertFalse(Functions::isString(new stdClass()));
    }

    /**
     * @covers \core\lib\Functions::isArray
     * @uses \core\lib\Set
     */
    public function testIsArray()
    {

        $this->assertTrue(Functions::isArray([1, 2, 3]));
        $this->assertTrue(Functions::isArray([]));


        $this->assertFalse(Functions::isArray('abc'));
        $this->assertFalse(Functions::isArray(42));
        $this->assertFalse(Functions::isArray(new Set([])));
        $this->assertFalse(Functions::isArray(false));
        $this->assertFalse(Functions::isArray(null));
        $this->assertFalse(Functions::isArray(42.5));
    }

    /**  
     *  @covers \core\lib\Functions::isFunction  
     * @uses \core\lib\Set  
     */
    public function testIsFunction()
    {

        $this->assertTrue(Functions::isFunction(function () { }));
        $this->assertTrue(Functions::isFunction('strlen'));


        $this->assertFalse(Functions::isFunction('abc'));
        $this->assertFalse(Functions::isFunction(42));
        $this->assertFalse(Functions::isFunction(new Set([])));
    }

    /**
     * @covers \core\lib\Functions::isWholeNumber
     * @uses \core\Regexp
     */
    public function testIsWholeNumber()
    {

        $this->assertTrue(Functions::isWholeNumber(0));
        $this->assertTrue(Functions::isWholeNumber(1));
        $this->assertTrue(Functions::isWholeNumber('42'));


        $this->assertFalse(Functions::isWholeNumber(-1));
        $this->assertFalse(Functions::isWholeNumber(3.14));
        $this->assertFalse(Functions::isWholeNumber('abc'));
    }

    /**
     * @covers \core\lib\Functions::isSet
     * @uses \core\lib\Set
     */
    public function testIsSet()
    {

        $this->assertTrue(Functions::isSet(new Set([])));
        $this->assertTrue(Functions::isSet(new Set([1, 2, 3])));


        $this->assertFalse(Functions::isSet([]));
        $this->assertFalse(Functions::isSet('abc'));
        $this->assertFalse(Functions::isSet(42));
    }

    /**
     * @covers \core\lib\Functions::createSetFromArray
     * @uses \core\lib\Set
     */
    public function testCreateSetFromArray()
    {

        $array = [1, 2, 3];
        $set = Functions::createSetFromArray($array);
        $this->assertInstanceOf(Set::class, $set);
        foreach ($array as $value) {
            $this->assertTrue($set->has($value));
        }


        $this->expectException(InvalidArgumentException::class);
        Functions::createSetFromArray('abc');
    }

    /**
     * @covers \core\lib\Functions::createSetFromFormula
     * @uses \core\lib\Set
     */
    public function testCreateSetFromFormula()
    {

        $start = 1;
        $end = 5;
        $formula = function ($x) {
            return $x * 2;
        };
        $set = Functions::createSetFromFormula($start, $end, $formula);
        $this->assertInstanceOf(Set::class, $set);
        for ($i = $start; $i <= $end; $i++) {
            $this->assertTrue($set->has($formula($i)));
        }


        $this->expectException(InvalidArgumentException::class);
        Functions::createSetFromFormula('a', 'b', 'c');
    }

    /**
     * @covers \core\lib\Functions::isEmpty
     * @uses \core\lib\Set
     */
    public function testIsEmpty()
    {

        $this->assertTrue(Functions::isEmpty(new Set([])));


        $this->assertFalse(Functions::isEmpty(new Set([1, 2, 3])));


        $this->expectException(InvalidArgumentException::class);
        Functions::isEmpty(42);
    }

    /**
     * @covers \core\lib\Functions::isElementOf
     * @uses \core\lib\Set
     */
    public function testIsElementOf()
    {

        $set = new Set([1, 2, 3]);
        $this->assertTrue(Functions::isElementOf(1, $set));
        $this->assertTrue(Functions::isElementOf(2, $set));
        $this->assertTrue(Functions::isElementOf(3, $set));


        $this->assertFalse(Functions::isElementOf(0, $set));
        $this->assertFalse(Functions::isElementOf(4, $set));



        $this->expectException(InvalidArgumentException::class);
        Functions::isElementOf('abc', $set);
        Functions::isElementOf('a', 'b');
    }

    /**
     * @covers \core\lib\Functions::isNotElementOf
     * @uses \core\lib\Set
     */
    public function testIsNotElementOf()
    {

        $set = new Set([1, 2, 3]);
        $this->assertTrue(Functions::isNotElementOf(0, $set));
        $this->assertTrue(Functions::isNotElementOf(4, $set));



        $this->assertFalse(Functions::isNotElementOf(1, $set));
        $this->assertFalse(Functions::isNotElementOf(2, $set));
        $this->assertFalse(Functions::isNotElementOf(3, $set));


        $this->expectException(InvalidArgumentException::class);
        Functions::isNotElementOf('abc', $set);
        Functions::isNotElementOf('a', 'b');
    }

    /**
     * @covers \core\lib\Functions::difference
     * @uses \core\lib\Set
     */
    public function testDifference()
    {

        $setA = new Set([1, 2, 3]);
        $setB = new Set([2, 4]);
        $diff = Functions::difference($setA, $setB);
        $this->assertInstanceOf(Set::class, $diff);
        foreach ([1, 3] as $value) {
            $this->assertTrue($diff->has($value));
            foreach ([2, 4] as $value) {
                $this->assertFalse($diff->has($value));
            }


            $this->expectException(InvalidArgumentException::class);
            Functions::difference('a', 'b');

        }
    }

    /**
     * @covers \core\lib\Functions::areEqual
     * @uses \core\lib\Set
     */
    public function testAreEqual()
    {

        $this->assertTrue(Functions::areEqual(new Set([1, 2, 3]), new Set([3, 2, 1])));
        $this->assertTrue(Functions::areEqual(new Set([]), new Set([])));


        $this->assertTrue(Functions::areEqual(new Set([1, 2, 3]), new Set([3, 2, 1])));
        $this->assertTrue(Functions::areEqual(new Set([]), new Set([])));


        $this->assertFalse(Functions::areEqual(new Set([1, 2, 3]), new Set([4, 5, 6])));
        $this->assertFalse(Functions::areEqual(new Set([1, 2]), new Set([1, 2, 3])));


        $this->expectException(InvalidArgumentException::class);
        Functions::areEqual('a', 'b');
    }


    /**
     * @covers \core\lib\Functions::isSubsetOf
     * @uses \core\lib\Set
     */
    public function testIsSubsetOf()
    {

        $this->assertTrue(Functions::isSubsetOf(new Set([1, 2]), new Set([1, 2, 3])));
        $this->assertTrue(Functions::isSubsetOf(new Set([]), new Set([1, 2, 3])));


        $this->assertFalse(Functions::isSubsetOf(new Set([1, 2, 4]), new Set([1, 2, 3])));
        $this->assertFalse(Functions::isSubsetOf(new Set([1, 2, 3]), new Set([1, 2])));


        $this->expectException(InvalidArgumentException::class);
        Functions::isSubsetOf('a', 'b');
    }

    /**
     * @covers \core\lib\Functions::isRealSubsetOf
     * @uses \core\lib\Set
     */
    public function testIsRealSubsetOf()
    {

        $this->assertTrue(Functions::isRealSubsetOf(new Set([1, 2]), new Set([1, 2, 3])));
        $this->assertTrue(Functions::isRealSubsetOf(new Set([]), new Set([1, 2, 3])));


        $this->assertFalse(Functions::isRealSubsetOf(new Set([1, 2, 3]), new Set([1, 2, 3])));
        $this->assertFalse(Functions::isRealSubsetOf(new Set([1, 2, 4]), new Set([1, 2, 3])));
        $this->assertFalse(Functions::isRealSubsetOf(new Set([1, 2, 3]), new Set([1, 2])));


        $this->expectException(InvalidArgumentException::class);
        Functions::isRealSubsetOf('a', 'b');
    }

    /**
     * @covers \core\lib\Functions::complement
     * @uses \core\lib\Set
     */
    public function testComplement()
    {

        $set = new Set([1, 2, 3]);
        $universe = new Set([1, 2, 3, 4, 5]);
        $comp = Functions::complement($set, $universe);
        $this->assertInstanceOf(Set::class, $comp);
        foreach ([4, 5] as $value) {
            $this->assertTrue($comp->has($value));
            foreach ([1, 2, 3] as $value) {
                $this->assertFalse($comp->has($value));
            }


            $this->expectException(InvalidArgumentException::class);
            Functions::complement('a', 'b');

        }
    }

    /**
     * @covers \core\lib\Functions::union
     * @uses \core\lib\Set
     */
    public function testUnion()
    {

        $setA = new Set([1, 2]);
        $setB = new Set([3]);
        $setC = new Set([4]);
        $union = Functions::union($setA, $setB, $setC);
        $this->assertInstanceOf(Set::class, $union);
        foreach ([1, 2, 3, 4] as $value) {
            $this->assertTrue($union->has($value));
        }


        $this->expectException(InvalidArgumentException::class);
        Functions::union('a', 'b', 'c');
    }

    /**
     * @covers \core\lib\Functions::intersection
     * @uses \core\lib\Set
     */
    public function testIntersection()
    {

        $setA = new Set([1, 2, 3]);
        $setB = new Set([2, 3, 4]);
        $setC = new Set([3, 4, 5]);
        $intersection = Functions::intersection($setA, $setB, $setC);
        $this->assertInstanceOf(Set::class, $intersection);
        foreach ([3] as $value) {
            $this->assertTrue($intersection->has($value));
            foreach ([1, 2, 4, 5] as $value) {
                $this->assertFalse($intersection->has($value));
            }


            $this->expectException(InvalidArgumentException::class);
            Functions::intersection('a', 'b', 'c');

        }
    }

    /**
     * @covers \core\lib\Functions::cardinality
     * @uses \core\lib\Set
     */
    public function testCardinality()
    {

        $this->assertEquals(0, Functions::cardinality(new Set([])));
        $this->assertEquals(3, Functions::cardinality(new Set([1, 2, 3])));


        $this->expectException(InvalidArgumentException::class);
        Functions::cardinality('a');

    }

    /**
     * @covers \core\lib\Functions::addElement
     * @uses \core\lib\Set
     */
    public function testAddElement()
    {

        $set = new Set([1, 2]);
        $this->assertTrue(Functions::addElement(3, $set));
        $this->assertTrue($set->has(3));


        $this->assertTrue(Functions::addElement(2, $set));
        $this->assertEquals(3, $set->size());


        $this->expectException(InvalidArgumentException::class);
        Functions::addElement('a', 'b');

    }

    /**
     * @covers \core\lib\Functions::delElement
     * @uses \core\lib\Set
     */
    public function testDelElement()
    {

        $set = new Set([1, 2]);
        $this->assertTrue(Functions::delElement(2, $set));
        $this->assertFalse($set->has(2));


        $this->assertTrue(Functions::delElement(3, $set));
        $this->assertEquals(1, $set->size());


        $this->expectException(InvalidArgumentException::class);
        Functions::delElement('a', 'b');

    }
    /**
     * @covers \core\lib\Functions::venn
     * @uses \core\lib\Set
     */
    public function testVennWithTwoSets()
    {

        $setA = new Set([1, 2, 3]);
        $setB = new Set([2, 3, 4]);


        $image = Functions::venn($setA, $setB);


        $this->assertNotNull($image);


        $this->assertStringStartsWith('data:image/png;base64,', $image);
    }

    /**
     * @covers \core\lib\Functions::venn
     * @uses \core\lib\Set
     */
    public function testVennWithThreeSets()
    {

        $setA = new set([1, 2, 3]);
        $setB = new set([2, 3, 4]);
        $setC = new set([3, 4, 5]);


        $image = Functions::venn($setA, $setB, $setC);


        $this->assertNotNull($image);


        $this->assertStringStartsWith('data:image/png;base64,', $image);
    }
    /**
     * @covers \core\lib\Functions::venn
     * @uses \core\lib\Set
     */
    public function testVennWithMoreThanThreeSets()
    {

        $setA = new set([1, 2, 3]);
        $setB = new set([2, 3, 4]);
        $setC = new set([3, 4, 5]);
        $setD = new set([4, 5, 6]);

        $this->expectException(InvalidArgumentException::class);

        Functions::venn($setA, $setB, $setC,$setD);

    }
}