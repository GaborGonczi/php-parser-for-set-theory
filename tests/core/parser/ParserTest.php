<?php

use core\lib\datastructures\Map;
use core\lib\datastructures\Point;
use core\lib\datastructures\Set;
use PHPUnit\Framework\TestCase;
use \core\parser\Parser;
use \core\parser\Token;
use core\parser\exception\ParserException;
use \app\server\classes\Env;

class ParserTest extends TestCase
{
    private $reflectionObject;

    protected function setUp(): void
    {
        (new Env(dirname(dirname(dirname(dirname(__FILE__)))).'/.env',true))->load();
        $this->reflectionObject = new ReflectionClass('\core\parser\Parser');
    }
    protected function tearDown(): void
    {
        unset($this->reflectionObject);
    }

    // Test the lexer with different inputs using a data provider
    /**
     * @dataProvider inputProvider
     */
    public function testParserWithDifferentInputs($tokens, $definedVars,$expected, $resultMap, $exception)
    {
        $parser = new Parser($tokens);
        $internalMap = $this->reflectionObject->getProperty('vars');
        $internalMap->setAccessible(true);
        $internalMap->setValue($parser, $definedVars);
        $result = $parser->parse();
        if ($exception !== null) {
            $this->expectException($exception);
        }
        if ($exception === null) {
            $this->assertSame($expected, $result);
        }
        if ($resultMap !== null) {
            $this->assertObjectEquals($parser->getVars(), $resultMap,'areEqual');

        }

    }

    public static function inputProvider()
    {
        $tests = [
            [
                [
                    ['type' => 'number', 'value' => floatval(3)],
                    ['type' => 'elementof', 'value' => '&isin;'],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A' => new Set([])]),
                'false',
                null,
                null
            ],
            [
                [
                    ['type' => 'minus', 'value' => '-'],
                    ['type' => 'number', 'value' => floatval(2)],
                    ['type' => 'notelementof', 'value' => '&notin;'],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A' => new Set([])]),
                'true',
                null,
                null
            ],
            [
                [
                    ['type' => 'number', 'value' => floatval(1)],
                    ['type' => 'elementof', 'value' => '&isin;'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['B' => new Set([1, 2, 3])]),
                'true',
                null,
                null,
                new Set([1,2,3])
            ],
            [
                [
                    ['type' => 'number', 'value' => floatval(3)],
                    ['type' => 'notelementof', 'value' => '&notin;'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['B' => new Set([1, 2, 3])]),
                'false',
                null,
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'tobeequal', 'value' => ':='],
                    ['type' => 'leftcurlybrace', 'value' => '{'],
                    ['type' => 'rightcurlybrace', 'value' => '}'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map([]),
                '{}',
                new Map(['A' => new Set([])]),
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'tobeequal', 'value' => ':='],
                    ['type' => 'leftcurlybrace', 'value' => '{'],
                    ['type' => 'number', 'value' => floatval(1)],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'number', 'value' => floatval(2)],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'number', 'value' => floatval(3)],
                    ['type' => 'rightcurlybrace', 'value' => '}'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map([]),
                '{1,2,3}',
                new Map(['B' => new Set([1,2,3])]),
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'dot', 'value' => '.'],
                    ['type' => 'add', 'value' => 'add'],
                    ['type' => 'leftparenthesis', 'value' => '('],
                    ['type' => 'number', 'value' => floatval(2)],
                    ['type' => 'rightparenthesis', 'value' => ')'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A'=>new Set([1])]),
                'true',
                new Map(['A'=>new Set([1,2])]),
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'dot', 'value' => '.'],
                    ['type' => 'delete', 'value' => 'delete'],
                    ['type' => 'leftparenthesis', 'value' => '('],
                    ['type' => 'number', 'value' => floatval(1)],
                    ['type' => 'rightparenthesis', 'value' => ')'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['B'=>new Set([1])]),
                'true',
                new Map(['B'=>new Set([])]),
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'dot', 'value' => '.'],
                    ['type' => 'delete', 'value' => 'delete'],
                    ['type' => 'leftparenthesis', 'value' => '('],
                    ['type' => 'number', 'value' => floatval(1)],
                    ['type' => 'rightparenthesis', 'value' => ')'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['B'=>new Set([])]),
                'true',
                new Map(['B'=>new Set([])]),
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'tobeequal', 'value' => ':='],
                    ['type' => 'leftcurlybrace', 'value' => '{'],
                    ['type' => 'identifier', 'value' => 'x'],
                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'x'],
                    ['type' => 'greaterthan', 'value' => '>'],
                    ['type' => 'number', 'value' => floatval(1)],
                    ['type' => 'land', 'value' => '&and;'],
                    ['type' => 'identifier', 'value' => 'x'],
                    ['type' => 'lessthanorequal', 'value' => '<='],
                    ['type' => 'number', 'value' => floatval(3)],
                    ['type' => 'rightcurlybrace', 'value' => '}'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map([]),
                '{2,3}',
                new Map(['C'=>new Set([2,3])]),
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'tobeequal', 'value' => ':='],
                    ['type' => 'leftcurlybrace', 'value' => '{'],
                    ['type' => 'identifier', 'value' => 'y'],
                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'z'],
                    ['type' => 'greaterthanorequal', 'value' => '>='],
                    ['type' => 'number', 'value' => floatval(0)],
                    ['type' => 'land', 'value' => '&and;'],
                    ['type' => 'identifier', 'value' => 'z'],
                    ['type' => 'lessthan', 'value' => '<'],
                    ['type' => 'number', 'value' => floatval(3)],
                    ['type' => 'land', 'value' => '&and;'],
                    ['type' => 'identifier', 'value' => 'y'],
                    ['type' => 'arrow', 'value' => '->'],
                    ['type' => 'number', 'value' => floatval(2)],
                    ['type' => 'multiply', 'value' => '*'],
                    ['type' => 'identifier', 'value' => 'z'],
                    ['type' => 'rightcurlybrace', 'value' => '}'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map([]),
                '{0,2,4}',
                new Map(['D'=>new Set([0,2,4])]),
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'E'],
                    ['type' => 'tobeequal', 'value' => ':='],
                    ['type' => 'leftcurlybrace', 'value' => '{'],
                    ['type' => 'identifier', 'value' => 'y'],
                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'z'],
                    ['type' => 'greaterthan', 'value' => '>'],
                    ['type' => 'number', 'value' => floatval(0)],
                    ['type' => 'land', 'value' => '&and;'],
                    ['type' => 'identifier', 'value' => 'z'],
                    ['type' => 'lessthanorequal', 'value' => '<='],
                    ['type' => 'number', 'value' => floatval(100)],
                    ['type' => 'land', 'value' => '&and;'],
                    ['type' => 'identifier', 'value' => 'y'],
                    ['type' => 'arrow', 'value' => '->'],
                    ['type' => 'identifier', 'value' => 'z'],
                    ['type' => 'divide', 'value' => '/'],
                    ['type' => 'number', 'value' => floatval(10)],
                    ['type' => 'rightcurlybrace', 'value' => '}'],
                    ['type' => 'eol', 'value' => '$']

                ],
                new Map([]),
                '{1,2,3,4,5,6,7,8,9,10}',
                new Map(['E'=>new Set([1,2,3,4,5,6,7,8,9,10])]),
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'F'],
                    ['type' => 'tobeequal', 'value' => ':='],
                    ['type' => 'leftcurlybrace', 'value' => '{'],
                    ['type' => 'identifier', 'value' => 'i'],
                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'i'],
                    ['type' => 'greaterthanorequal', 'value' => '>='],
                    ['type' => 'number', 'value' => floatval(0)],
                    ['type' => 'land', 'value' => '&and;'],
                    ['type' => 'identifier', 'value' => 'i'],
                    ['type' => 'lessthanorequal', 'value' => '<='],
                    ['type' => 'number', 'value' => floatval(20)],
                    ['type' => 'land', 'value' => '&and;'],
                    ['type' => 'leftparenthesis', 'value' => '('],
                    ['type' => 'number', 'value' => floatval(5)],
                    ['type' => 'divides', 'value' => '&mid;'],
                    ['type' => 'identifier', 'value' => 'i'],
                    ['type' => 'lor', 'value' => '&or;'],
                    ['type' => 'number', 'value' => floatval(7)],
                    ['type' => 'divides', 'value' => '&mid;'],
                    ['type' => 'identifier', 'value' => 'i'],
                    ['type' => 'land', 'value' => '&and;'],
                    ['type' => 'number', 'value' => floatval(10)],
                    ['type' => 'doesnotdivide', 'value' => '&nmid;'],
                    ['type' => 'identifier', 'value' => 'i'],
                    ['type' => 'rightparenthesis', 'value' => ')'],
                    ['type' => 'rightcurlybrace', 'value' => '}'],
                    ['type' => 'eol', 'value' => '$']

                ],
                new Map([]),
                '{0,5,7,10,14,15,20}',
                new Map(['F'=>new Set([0,5,7,10,14,15,20])]),
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'equal', 'value' => '='],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A'=>new Set([1,2]),'B'=>new Set([1,2])]),
                'true',
                null,
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'equal', 'value' => '='],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['C'=>new Set([2,3]),'B'=>new Set([1,2])]),
                'false',
                null,
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'subsetof', 'value' => '&sube;'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A'=>new Set([1,2]),'B'=>new Set([1,2])]),
                'true',
                null,
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'subsetof', 'value' => '&sube;'],
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['C'=>new Set([2,3]),'D'=>new Set([4,5])]),
                'false',
                null,
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'realsubsetof', 'value' => '&sub;'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A'=>new Set([1,2]),'B'=>new Set([1,2])]),
                'false',
                null,
                null
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'realsubsetof', 'value' => '&sub;'],
                    ['type' => 'identifier', 'value' => 'H'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A'=>new Set([1,2]),'H'=>new Set([0,1,2,3,4,5])]),
                'true',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'H'],
                    ['type' => 'realsubsetof', 'value' => '&sub;'],
                    ['type' => 'identifier', 'value' => 'H'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['H'=>new Set([0,1,2,3,4,5])]),
                'false',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'complement', 'value' => '&comp;'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A'=>new Set([1,2]),'H'=>new Set([0,1,2,3,4,5])]),
                '{0,3,4,5}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'H'],
                    ['type' => 'complement', 'value' => '&comp;'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['H'=>new Set([0,1,2,3,4,5])]),
                '{}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'complement', 'value' => '&comp;'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['D'=>new Set([4,5]),'H'=>new Set([0,1,2,3,4,5])]),
                '{0,1,2,3}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A'=>new Set([1,2]),'D'=>new Set([4,5])]),
                '{1,2,4,5}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['B'=>new Set([1,2]),'C'=>new Set([2,3])]),
                '{1,2,3}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['C'=>new Set([2,3]),'D'=>new Set([4,5])]),
                '{2,3,4,5}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A'=>new Set([1,2]),'B'=>new Set([1,2]),'C'=>new Set([2,3]),'D'=>new Set([4,5])]),
                '{1,2,3,4,5}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'intersection', 'value' => '&cap;'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['B'=>new Set([1,2]),'C'=>new Set([2,3])]),
                '{2}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'intersection', 'value' => '&cap;'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['C'=>new Set([2,3]),'D'=>new Set([4,5])]),
                '{}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'setminus', 'value' => '&setminus;'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['B'=>new Set([1,2]),'C'=>new Set([2,3])]),
                '{1}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'setminus', 'value' => '&setminus;'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['B'=>new Set([1,2]),'C'=>new Set([2,3])]),
                '{3}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'H'],
                    ['type' => 'setminus', 'value' => '&setminus;'],
                    ['type' => 'leftparenthesis', 'value' => '('],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'rightparenthesis', 'value' => ')'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['H'=>new Set([0,1,2,3,4,5]),'A'=>new Set([1,2]),'B'=>new Set([1,2]),'C'=>new Set([2,3]),'D'=>new Set([4,5])]),
                '{0}',
                null,
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'tobeequal', 'value' => ':='],
                    ['type' => 'leftsquarebracket', 'value' => '['],
                    ['type' => 'number', 'value' => floatval(1)],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'number', 'value' => floatval(2)],
                    ['type' => 'rightsquarebracket', 'value' => ']'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map([]),
                '[1,2]',
                new Map(['C'=>new Point(1,2)]),
                null
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'tobeequal', 'value' => ':='],
                    ['type' => 'leftsquarebracket', 'value' => '['],
                    ['type' => 'minus', 'value' => '-'],
                    ['type' => 'number', 'value' => floatval(2)],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'minus', 'value' => '-'],
                    ['type' => 'number', 'value' => floatval(8)],
                    ['type' => 'rightsquarebracket', 'value' => ']'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map([]),
                '[-2,-8]',
                new Map(['D'=>new Point(-2,-8)]),
                null,
                new Set([])
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'tobeequal', 'value' => ':='],
                    ['type' => 'leftcurlybrace', 'value' => '{'],
                    ['type' => 'leftsquarebracket', 'value' => '['],
                    ['type' => 'number', 'value' => floatval(3)],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'minus', 'value' => '-'],
                    ['type' => 'number', 'value' => floatval(4)],
                    ['type' => 'rightsquarebracket', 'value' => ']'],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'leftsquarebracket', 'value' => '['],
                    ['type' => 'number', 'value' => floatval(5)],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'minus', 'value' => '-'],
                    ['type' => 'number', 'value' => floatval(6)],
                    ['type' => 'rightsquarebracket', 'value' => ']'],
                    ['type' => 'rightcurlybrace', 'value' => '}'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map([]),
                '{[3,-4],[5,-6]}',
                new Map(['A'=>new Set([new Point(3,-4),new Point(5,-6)])]),
                null,
                new Set([])
            ],
            [
                [

                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'tobeequal', 'value' => ':='],
                    ['type' => 'leftcurlybrace', 'value' => '{'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'rightcurlybrace', 'value' => '}'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['C'=>new Point(1,2),'D'=>new Point(-2,-8)]),
                '{[1,2],[-2,-8]}',
                new Map(['B'=>new Set([new Point(1,2),new Point(-2,-8)]),'C'=>new Point(1,2),'D'=>new Point(-2,-8)]),
                null,
                new Set([])
            ],
            [
                [

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'H'],
                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['H'=>new Set([0,1,2,3,4,5])]),
                '6',
                null,
                null,
                new Set([0,1,2,3,4,5])
            ],
            [
                [

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['C'=>new Set([2,3])]),
                '2',
                null,
                null,
                new Set([2,3])
            ],
            [
                [

                    ['type' => 'leftparenthesis', 'value' => '('],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'intersection', 'value' => '&cap;'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'rightparenthesis', 'value' => ')'],
                    ['type' => 'complement', 'value' => '&comp;'],
                    ['type' => 'eol', 'value' => '$']
                   
                ],
                new Map(['A'=>new Set([1,2]),'C'=>new Set([2,3])]),
                'H is not defined. Please define it and rerun the expression evaluation.',
                null,
                null,
                new Set([1,2,3])
            ],
            [
                [
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'complement', 'value' => '&comp;'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'complement', 'value' => '&comp;'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['A'=>new Set([1,2]),'C'=>new Set([2,3])]),
                'H is not defined. Please define it and rerun the expression evaluation.',
                null,
                null,
                new Set([1,2,3])
            ],
            [
                [
                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'verticalline', 'value' => '|'], //2

                    ['type' => 'plus', 'value' => '+'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'verticalline', 'value' => '|'],//2

                    ['type' => 'plus', 'value' => '+'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'verticalline', 'value' => '|'],//2

                    ['type' => 'plus', 'value' => '+'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'verticalline', 'value' => '|'],//2

                    ['type' => 'minus', 'value' => '-'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'intersection', 'value' => '&cap;'],//2
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'minus', 'value' => '-'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'intersection', 'value' => '&cap;'],//1
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'minus', 'value' => '-'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'intersection', 'value' => '&cap;'],//0
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'minus', 'value' => '-'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'intersection', 'value' => '&cap;'],//1
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'minus', 'value' => '-'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'intersection', 'value' => '&cap;'],//0
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'minus', 'value' => '-'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'intersection', 'value' => '&cap;'],//0
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'plus', 'value' => '+'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'intersection', 'value' => '&cap;'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'intersection', 'value' => '&cap;'],//1
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'plus', 'value' => '+'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'intersection', 'value' => '&cap;'],//0
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'intersection', 'value' => '&cap;'],
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'plus', 'value' => '+'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'intersection', 'value' => '&cap;'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'intersection', 'value' => '&cap;'],//0
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'plus', 'value' => '+'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'intersection', 'value' => '&cap;'],
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'intersection', 'value' => '&cap;'],//0
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'minus', 'value' => '-'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'intersection', 'value' => '&cap;'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'intersection', 'value' => '&cap;'],
                    ['type' => 'identifier', 'value' => 'C'],//0
                    ['type' => 'intersection', 'value' => '&cap;'],
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'plus', 'value' => '+'],

                    ['type' => 'verticalline', 'value' => '|'],
                    ['type' => 'leftparenthesis', 'value' => '('],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'union', 'value' => '&cup;'],//1
                    ['type' => 'identifier', 'value' => 'C'],
                    ['type' => 'union', 'value' => '&cup;'],
                    ['type' => 'identifier', 'value' => 'D'],
                    ['type' => 'rightparenthesis', 'value' => ')'],
                    ['type' => 'complement', 'value' => '&comp;'],
                    ['type' => 'verticalline', 'value' => '|'],

                    ['type' => 'eol', 'value' => '$']
                ],
                new Map(['H'=> new Set([0,1,2,3,4,5]),'A'=>new Set([1,2]),'B'=>new Set([1,2]),'C'=>new Set([2,3]),'D'=>new Set([4,5])]),
                '6',
                null,
                null,
                new Set([0,1,2,3,4,5])
            ],
            [
                [
                    ['type' => 'leftcurlybrace', 'value' => '{'],
                    ['type' => 'number', 'value' => floatval(1)],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'number', 'value' => floatval(2)],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'number', 'value' => floatval(3)],
                    ['type' => 'rightcurlybrace', 'value' => '}'],
                    ['type' => 'eol', 'value' => '$']
                ],
                new Map([]),
                '{1,2,3}',
                null,
                null,
                new Set([])
            ]
            //Manually tested
            /*[
                [

                    ['type' => 'pointsetdiagram', 'value' => 'PointSetDiagram'],
                    ['type' => 'leftparenthesis', 'value' => '('],
                    ['type' => 'identifier', 'value' => 'A'],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'identifier', 'value' => 'B'],
                    ['type' => 'rightparenthesis', 'value' => ')'],
                    ['type' => 'eol', 'value' => '$']
                ],
                null
            ],
            [
                [

                    ['type' => 'pointsetdiagram', 'value' => 'PointSetDiagram'],
                    ['type' => 'leftparenthesis', 'value' => '('],
                    ['type' => 'leftcurlybrace', 'value' => '{'],
                    ['type' => 'leftsquarebracket', 'value' => '['],
                    ['type' => 'number', 'value' => floatval(3)],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'number', 'value' => floatval(5)],
                    ['type' => 'rightsquarebracket', 'value' => ']'],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'leftsquarebracket', 'value' => '['],
                    ['type' => 'minus', 'value' => '-'],
                    ['type' => 'number', 'value' => floatval(7)],
                    ['type' => 'comma', 'value' => ','],
                    ['type' => 'minus', 'value' => '-'],
                    ['type' => 'number', 'value' => floatval(8)],
                    ['type' => 'rightsquarebracket', 'value' => ']'],
                    ['type' => 'rightcurlybrace', 'value' => '}'],
                    ['type' => 'rightparenthesis', 'value' => ')'],
                    ['type' => 'eol', 'value' => '$']
                ],
                null
            ],*/
            
        ];
        $htmlEntityMap=[
            "∈"=>"&isin;",
            "∉"=>"&notin;",
            "⊆"=>"&sube;",
            "⊂"=>"&sub;",
            "∁"=>"&comp;",
            "∪"=>"&cup;",
            "∩"=>"&cap;",
            "∧"=>"&and;",
            "∨"=>"&or;",
            "∖"=>"&setminus;",
            "∣"=>"&mid;",
            "∤"=>"&nmid;",
            
        ];
        foreach ($tests as &$test) {
           $tokens=$test[0];
           foreach ($htmlEntityMap as $key => $value) {
              foreach ($tokens as &$token) {
                if($token['value']==$key) {
                    $token['value']=$value;
                }
              }
            }
    
        }
        return $tests;
    }

    /**
    * @dataProvider matchProvider
    */
        
    public function testGetExpectedForMatch($value, $expected) {
        $method=$this->reflectionObject->getMethod('getExpectedForMatch');      
        $method->setAccessible(true);
        $object=$this->reflectionObject->newInstance();
        $result = $method->invoke($object, $value);
        
        $this->assertSame($expected, $result);
    }
    public static function matchProvider() {
        $tests= [
            ['12',['name'=>Token::NUMBER['name'],'value'=>'12']],
            ['.',Token::DOT],
            ['+',Token::PLUS],
            ['-',Token::MINUS],
            ['*',Token::MULTIPLY],
            ['/',Token::DIVIDE],
            [':=',Token::TOBEEQUAL],
            ['∈',Token::ELEMENTOF],
            ['∉',Token::NOTELEMENTOF],
            ['=',Token::EQUAL],
            ['⊆',Token::SUBSETOF],
            ['⊂',Token::REALSUBSETOF],
            ['∁',Token::COMPLEMENT],
            ['∪',Token::UNION],
            ['∩',Token::INTERSECTION],
            ['∖',Token::SETMINUS],
            [',',Token::COMMA],
            ['∣',Token::DIVIDES],
            ['∤',Token::DOESNOTDIVIDE],
            ['|',Token::VERTICALLINE],
            ['∧',Token::LAND],
            ['∨',Token::LOR],
            ['{',Token::LEFTCURLYBRACE],
            ['}',Token::RIGHTCURLYBRACE],
            ['(',Token::LEFTPARENTHESIS],
            [')',Token::RIGHTPARENTHESIS],
            ['[',Token::LEFTSQUAREBRACKET],
            [']',Token::RIGHTSQUAREBRACKET],
            ['<',Token::LESSTHAN],
            ['>',Token::GREATERTHAN],
            ['<=',Token::LESSTHANOREQUAL],
            ['>=',Token::GREATERTHANOREQUAL],
            ['Universe',['name'=>Token::IDENTIFIER['name'],'value'=>'Universe']],
            ['$',Token::EOL],
            ['Venn',Token::VENN],
            ['PointSetDiagram',Token::POINTSETDIAGRAM],
            ['add',Token::ADD],
            ['delete',Token::DELETE],
            ['?', null]
        ];

        $htmlEntityMap=[
            "∈"=>"&isin;",
            "∉"=>"&notin;",
            "⊆"=>"&sube;",
            "⊂"=>"&sub;",
            "∁"=>"&comp;",
            "∪"=>"&cup;",
            "∩"=>"&cap;",
            "∧"=>"&and;",
            "∨"=>"&or;",
            "∖"=>"&setminus;",
            "∣"=>"&mid;",
            "∤"=>"&nmid;",
            
        ];
        foreach ($tests as &$test) {
           $token=$test[0];
           foreach ($htmlEntityMap as $key => $value) {
              if($token===$key){
                $test[0]=$value;   
              }
            }
             
        }
        return $tests;
    }
}
    