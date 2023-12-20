<?php

use core\lib\Map;
use core\lib\Point;
use \PHPUnit\Framework\TestCase;
use \core\lib\Functions;
use \core\lib\Set;
use \core\parser\Token;
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


    public function testIsEmptyArray(): void
    {
        $this->assertTrue(Functions::isEmptyArray([]));
        $this->assertFalse(Functions::isEmptyArray([1, 2, 3]));
    }


    public function testIsNotEmptyArray(): void
    {
        $this->assertTrue(Functions::isNotEmptyArray([1, 2, 3]));
        $this->assertFalse(Functions::isNotEmptyArray([]));
    }


    public function testIsObject(): void
    {

        $this->assertTrue(Functions::isObject(new Set([])));
        $this->assertTrue(Functions::isObject(new Point(1, 2)));
        $this->assertTrue(Functions::isObject(new Map(["A" => new Set([1, 2])])));
        $this->assertFalse(Functions::isObject("foo"));
        $this->assertFalse(Functions::isObject(true));
        $this->assertFalse(Functions::isObject(null));
        $this->assertFalse(Functions::isObject(3));
        $this->assertFalse(Functions::isObject(3.14));
        $this->assertFalse(Functions::isObject([]));
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
     *  @covers \core\lib\Functions::isNull  
     * @uses \core\lib\Set  
     */
    public function testIsNull()
    {

        $this->assertTrue(Functions::isNull(null));

        $this->assertFalse(Functions::isNull(function () { }));
        $this->assertFalse(Functions::isNull('strlen'));
        $this->assertFalse(Functions::isNull('abc'));
        $this->assertFalse(Functions::isNull(42));
        $this->assertFalse(Functions::isNull(42.6));
        $this->assertFalse(Functions::isNull(new Set([])));
        $this->assertFalse(Functions::isNull(true));
        $this->assertFalse(Functions::isNull(false));
        $this->assertFalse(Functions::isNull([]));
        $this->assertFalse(Functions::isNull("null"));
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
        $this->assertTrue(Functions::isWholeNumber(-1));

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
     * @covers \core\lib\Functions::IsGoodOperation
     * @uses \core\lib\Set
     */
    public function testIsGoodOperation()
    {
        $goodoperations = array("+", "-");

        $this->assertTrue(Functions::IsGoodOperation('+', $goodoperations));
        $this->assertTrue(Functions::IsGoodOperation('-', $goodoperations));
        $this->assertFalse(Functions::IsGoodOperation('*', $goodoperations));
        $this->assertFalse(Functions::IsGoodOperation('/', $goodoperations));


        $this->expectException(InvalidArgumentException::class);
        Functions::IsGoodOperation(1, "not an array");
    }
    /**
     * @covers \core\lib\Functions::removeNullFromArray
     */
    public function testRemoveNullFromArray()
    {

        $this->assertIsArray(Functions::removeNullFromArray([]));


        $this->expectException(InvalidArgumentException::class);
        Functions::removeNullFromArray("not an array");


        $this->assertEquals([1, 2, 3], Functions::removeNullFromArray([1, null, 2, null, 3]));
    }
    /**
     * @covers \core\lib\Functions::removeEmptyArrayFromArray
     */
    public function testRemoveEmptyArrayFromArray(): void
    {
        $this->assertIsArray(Functions::removeEmptyArrayFromArray([]));
        $this->expectException(InvalidArgumentException::class);
        Functions::removeEmptyArrayFromArray("not an array");
        $this->assertEquals([1, [2, 3], 4], Functions::removeEmptyArrayFromArray([1, [], [2, 3], [], 4]));
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
        $boundformula = function ($x) {
            return $x > 1 && $x <= 3 || $x == 5;
        };
        $set = Functions::createSetFromFormula($start, $end, $boundformula);
        $this->assertInstanceOf(Set::class, $set);
        for ($i = $start; $i <= $end; $i++) {
            if ($boundformula($i)) {
                $this->assertTrue($set->has($i));
            }

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
        }
        foreach ([1, 2, 4, 5] as $value) {
            $this->assertFalse($intersection->has($value));
        }


        $this->expectException(InvalidArgumentException::class);
        Functions::intersection('a', 'b', 'c');

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
     * @covers \core\lib\Functions::deleteElement
     * @uses \core\lib\Set
     */
    public function testDeleteElement()
    {

        $set = new Set([1, 2]);
        $this->assertTrue(Functions::deleteElement(2, $set));
        $this->assertFalse($set->has(2));


        $this->assertTrue(Functions::deleteElement(3, $set));
        $this->assertEquals(1, $set->size());


        $this->expectException(InvalidArgumentException::class);
        Functions::deleteElement('a', 'b');

    }

    /**
     * @covers \core\lib\Functions::createDivisibilityCondition
     * @uses \core\parser\Token
     */
    public function testCreateDivisibilityCondition()
    {

        $divisor = 3;
        $divides = Token::DIVIDES['value'];
        $doesnotdivide = Token::DOESNOTDIVIDE['value'];
        $num1 = 9;
        $num2 = 10;



        $divideCond = Functions::createDivisibilityCondition($divisor, $divides);
        $noDivideCond = Functions::createDivisibilityCondition($divisor, $doesnotdivide);


        $this->assertTrue($divideCond($num1));
        $this->assertTrue($noDivideCond($num2));
        $this->assertFalse($divideCond($num2));
        $this->assertFalse($noDivideCond($num1));



        $this->expectException(InvalidArgumentException::class);
        Functions::createDivisibilityCondition('a', 'b');
    }

     /**
     * @covers \core\lib\Functions::processLogicalRhs
     * @uses \core\parser\Token
     */
    public function testProcessLogicalRhs()
    {
        $input1 = ['num' => 2, 'simpleop' => '+', 'id' => 'x'];
        $output1 =Functions::processLogicalRhs($input1); /*['x' => [function($var) {return $var + 2;}]];*/

        $input2 = ['num' => [3, 0], 'simpleop' => '/'];
        $output2 =Functions::processLogicalRhs($input2); /*['constant' => function() {return Functions::illegalArguments('Functions::processLogicalRhs');}];*/

        $input3 = 5;

        $this->assertArrayHasKey('x', $output1);
        $this->assertIsCallable($output1['x'][0]);
        $output1Fun=$output1['x'][0];
        $this->assertEquals(5,$output1Fun(3));

        $this->assertArrayHasKey('constant', $output2);
        $this->assertIsCallable($output2['constant']);
        $output2Fun=$output2['constant'];
        $this->expectException(InvalidArgumentException::class);
        $output2Fun();
        $this->expectException(InvalidArgumentException::class);
        Functions::processLogicalRhs($input3);
    }


    /**
     * @covers \core\lib\Functions::createComparsionCondition
     * @uses \core\parser\Token
     */
    public function testCreateComparsionCondition()
    {
        
        $lessThan = Token::LESSTHAN['value'];
        $greaterThan = Token::GREATERTHAN['value'];
        $lessThanOrEqual = Token::LESSTHANOREQUAL['value'];
        $greaterThanOrEqual = Token::GREATERTHANOREQUAL['value'];
        $equal = Token::EQUAL['value'];
        $logicalrhsfuncs = ['constant' => function () { return 5; }];

        $num1 = 3;
        $num2 = 5;
        $num3 = 7;

        
        $lessThanCond = Functions::createComparsionCondition($lessThan, $logicalrhsfuncs);
        $greaterThanCond = Functions::createComparsionCondition($greaterThan, $logicalrhsfuncs);
        $lessThanOrEqualCond = Functions::createComparsionCondition($lessThanOrEqual, $logicalrhsfuncs);
        $greaterThanOrEqualCond = Functions::createComparsionCondition($greaterThanOrEqual, $logicalrhsfuncs);
        $equalCond = Functions::createComparsionCondition($equal, $logicalrhsfuncs);

        
        $this->assertTrue($lessThanCond($num1));
        $this->assertFalse($lessThanCond($num2));
        $this->assertFalse($lessThanCond($num3));

        $this->assertFalse($greaterThanCond($num1));
        $this->assertFalse($greaterThanCond($num2));
        $this->assertTrue($greaterThanCond($num3));

        $this->assertTrue($lessThanOrEqualCond($num1));
        $this->assertTrue($lessThanOrEqualCond($num2));
        $this->assertFalse($lessThanOrEqualCond($num3));

        $this->assertFalse($greaterThanOrEqualCond($num1));
        $this->assertTrue($greaterThanOrEqualCond($num2));
        $this->assertTrue($greaterThanOrEqualCond($num3));

        $this->assertFalse($equalCond($num1));
        $this->assertTrue($equalCond($num2));
        $this->assertFalse($equalCond($num3));

        
        $this->expectException(InvalidArgumentException::class);
        Functions::createComparsionCondition('a', 'b');
    }
    /**
     * @covers \core\lib\Functions::getMinMax
     */
    public function testGetMinMax()
    {
        $bounds1 = [10, 20, 30, 40, 50];
        $bounds2 = [4, 5];
        $minmax1 = ['start' => 10, 'end' => 50];
        $minmax2 = ['start' =>4,'end' =>5];
        $this->assertSame($minmax1, Functions::getMinMax($bounds1));
        $this->assertSame($minmax2, Functions::getMinMax($bounds2));


        $this->expectException(InvalidArgumentException::class);
        Functions::getMinMax('a');
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

        Functions::venn($setA, $setB, $setC, $setD);

    }
}