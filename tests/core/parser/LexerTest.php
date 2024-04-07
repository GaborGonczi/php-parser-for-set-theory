<?php

use PHPUnit\Framework\TestCase;
use \core\parser\Lexer;
use core\parser\exception\LexerException;
use \app\server\classes\Env;

class LexerTest extends TestCase
{
    private $reflectionObject;

    protected function setUp() : void 
    {
        (new Env(dirname(dirname(dirname(dirname(__FILE__)))).'/.env',true))->load();
        $this->reflectionObject=new ReflectionClass('\core\parser\Lexer');
    }
    protected function tearDown(): void
    {
        unset($this->reflectionObject);
    }
    public function testConstructorWithEmptyString()
    {
        $withEmptyinput=$this->reflectionObject->newInstance('');
        $inputProperty=$this->reflectionObject->getProperty('input');
        $this->assertSame('$', $inputProperty->getValue($withEmptyinput));
    }
    public function testConstructorWithNonEmptyString()
    {
        $withSomeinput=$this->reflectionObject->newInstance('someinput');
        $inputProperty=$this->reflectionObject->getProperty('input');
        $this->assertSame('someinput$', $inputProperty->getValue($withSomeinput));
    }

    // Test the lexer with different inputs using a data provider
    /**
     * @dataProvider inputProvider
     */
    public function testLexerWithDifferentInputs($input, $expected)
    {
        $lexer = new Lexer($input,true,'eng');
        $this->assertSame($expected, $lexer->getTokens());

        
    }

    public static function inputProvider()
    {
        $tests=[
            ['3∈A',[
                ['type' => 'number', 'value' => floatval(3)],
                ['type' => 'elementof', 'value' => '&isin;'],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'eol', 'value' => '$']
            ]],
           ['-2∉A',[
                ['type' => 'minus', 'value' => '-'],
                ['type' => 'number', 'value' => floatval(2)],
                ['type' => 'notelementof', 'value' => '&notin;'],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'eol', 'value' => '$']
           ]],
           ['1∈B',[
                ['type' => 'number', 'value' => floatval(1)],
                ['type' => 'elementof', 'value' => '&isin;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'eol', 'value' => '$']
           ]],
           ['3∉B',[
                ['type' => 'number', 'value' => floatval(3)],
                ['type' => 'notelementof', 'value' => '&notin;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'eol', 'value' => '$']
           ]],
           ['A:={}',[
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'tobeequal', 'value' => ':='],
                ['type' => 'leftcurlybrace', 'value' => '{'],
                ['type' => 'rightcurlybrace', 'value' => '}'],
                ['type' => 'eol', 'value' => '$']
           ]],
           ['B:={1,2,3}',[
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
            ]],
            ['A.add(2)',[
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'dot', 'value' => '.'],
                ['type' => 'add', 'value' => 'add'],
                ['type' => 'leftparenthesis', 'value' => '('],
                ['type' => 'number', 'value' => floatval(2)],
                ['type' => 'rightparenthesis', 'value' => ')'],
                ['type' => 'eol', 'value' => '$']
            ]],
            ['B.delete(1)',[
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'dot', 'value' => '.'],
                ['type' => 'delete', 'value' => 'delete'],
                ['type' => 'leftparenthesis', 'value' => '('],
                ['type' => 'number', 'value' => floatval(1)],
                ['type' => 'rightparenthesis', 'value' => ')'],
                ['type' => 'eol', 'value' => '$']
            ]],
            ['Venn(A,B)',[
                ['type' => 'venn', 'value' => 'Venn'],
                ['type' => 'leftparenthesis', 'value' => '('],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'rightparenthesis', 'value' => ')'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['C:={x|x>1∧x<=3}',[
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
                ['type' => 'number', 'value'=> floatval(3)],
                ['type' => 'rightcurlybrace', 'value' => '}'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['D:={y | z>=0 ∧ z<3 ∧ y->2*z}',[
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'tobeequal', 'value' => ':='],
                ['type' => 'leftcurlybrace', 'value' => '{'],
                ['type' => 'identifier', 'value' => 'y'],
                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'z'],
                ['type' => 'greaterthanorequal', 'value' => '>='],
                ['type' => 'number', 'value' => floatval( 0)],
                ['type' => 'land', 'value' => '&and;'],
                ['type' => 'identifier', 'value' => 'z'],
                ['type' => 'lessthan', 'value' => '<'],
                ['type' => 'number', 'value' => floatval( 3)],
                ['type' => 'land', 'value' => '&and;'],
                ['type' => 'identifier', 'value' => 'y'],
                ['type' => 'arrow', 'value' => '->'],
                ['type' => 'number', 'value' => floatval( 2)],
                ['type' => 'multiply', 'value' => '*'],
                ['type' => 'identifier', 'value' => 'z'],
                ['type' => 'rightcurlybrace', 'value' => '}'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['E:={y | z>0 ∧ z<=100 ∧ y->z/10}',[

                ['type' => 'identifier', 'value' => 'E'],
                ['type' => 'tobeequal', 'value' => ':='],
                ['type' => 'leftcurlybrace', 'value' => '{'],
                ['type' => 'identifier', 'value' => 'y'],
                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'z'],
                ['type' => 'greaterthan', 'value' => '>'],
                ['type' => 'number', 'value' => floatval( 0)],
                ['type' => 'land', 'value' => '&and;'],
                ['type' => 'identifier', 'value' => 'z'],
                ['type' => 'lessthanorequal', 'value' => '<='],
                ['type' => 'number', 'value' => floatval( 100)],
                ['type' => 'land', 'value' => '&and;'],
                ['type' => 'identifier', 'value' => 'y'],
                ['type' => 'arrow', 'value' => '->'],
                ['type' => 'identifier', 'value' => 'z'],
                ['type' => 'divide', 'value' => '/'],
                ['type' => 'number', 'value' => floatval( 10)],
                ['type' => 'rightcurlybrace', 'value' => '}'],
                ['type' => 'eol', 'value' => '$']

           ]],
            ['F:={i| i>=0∧i<=20∧(5∣i∨7∣i∧10∤i)}',[

                ['type' => 'identifier', 'value' => 'F'],
                ['type' => 'tobeequal', 'value' => ':='],
                ['type' => 'leftcurlybrace', 'value' => '{'],
                ['type' => 'identifier', 'value' => 'i'],
                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'i'],
                ['type' => 'greaterthanorequal', 'value' => '>='],
                ['type' => 'number', 'value' => floatval( 0)],
                ['type' => 'land', 'value' => '&and;'],
                ['type' => 'identifier', 'value' => 'i'],
                ['type' => 'lessthanorequal', 'value' => '<='],
                ['type' => 'number', 'value' => floatval( 20)],
                ['type' => 'land', 'value' => '&and;'],
                ['type' => 'leftparenthesis', 'value' => '('],
                ['type' => 'number', 'value' => floatval( 5)],
                ['type' => 'divides', 'value' => '&mid;'],
                ['type' => 'identifier', 'value' => 'i'],
                ['type' => 'lor', 'value' => '&or;'],
                ['type' => 'number', 'value' => floatval( 7)],
                ['type' => 'divides', 'value' => '&mid;'],
                ['type' => 'identifier', 'value' => 'i'],
                ['type' => 'land', 'value' => '&and;'],
                ['type' => 'number', 'value' => floatval( 10)],
                ['type' => 'doesnotdivide', 'value' => '&nmid;'],
                ['type' => 'identifier', 'value' => 'i'],
                ['type' => 'rightparenthesis', 'value' => ')'],
                ['type' => 'rightcurlybrace', 'value' => '}'],
                ['type' => 'eol', 'value' => '$']

           ]],
            ['A=B',[
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'equal', 'value' => '='],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'eol', 'value' => '$']
            ]],
            ['B=C',[
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'equal', 'value' => '='],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['A⊆B',[
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'subsetof', 'value' => '&sube;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['C⊆D',[
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'subsetof', 'value' => '&sube;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['A⊂B',[
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'realsubsetof', 'value' => '&sub;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['A⊂H',[
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'realsubsetof', 'value' => '&sub;'],
                ['type' => 'identifier', 'value' => 'H'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['H⊂H',[

                ['type' => 'identifier', 'value' => 'H'],
                ['type' => 'realsubsetof', 'value' => '&sub;'],
                ['type' => 'identifier', 'value' => 'H'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['A∁',[

                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'complement', 'value' => '&comp;'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['H∁',[

                ['type' => 'identifier', 'value' => 'H'],
                ['type' => 'complement', 'value' => '&comp;'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['D∁',[

                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'complement', 'value' => '&comp;'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['A∪D',[

                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['B∪C',[

                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['C∪D',[

                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['A∪B∪C∪D',[

                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['B∩C',[

                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['D∩C',[

                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['B∖C',[

                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'setminus', 'value' => '&setminus;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['C∖B',[

                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'setminus', 'value' => '&setminus;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['H∖(A∪B∪C∪D)',[

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
           ]],
            ['C:=[1,2]',[

                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'tobeequal', 'value' => ':='],
                ['type' => 'leftsquarebracket', 'value' => '['],
                ['type' => 'number', 'value' => floatval( 1)],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'number', 'value' => floatval( 2)],
                ['type' => 'rightsquarebracket', 'value' => ']'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['D:=[-2,-8]',[

                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'tobeequal', 'value' => ':='],
                ['type' => 'leftsquarebracket', 'value' => '['],
                ['type' => 'minus', 'value' => '-'],
                ['type' => 'number', 'value' => floatval( 2)],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'minus', 'value' => '-'],
                ['type' => 'number', 'value' => floatval( 8)],
                ['type' => 'rightsquarebracket', 'value' => ']'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['A:={[3,-4],[5,-6]}',[

                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'tobeequal', 'value' => ':='],
                ['type' => 'leftcurlybrace', 'value' => '{'],
                ['type' => 'leftsquarebracket', 'value' => '['],
                ['type' => 'number', 'value' => floatval( 3)],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'minus', 'value' => '-'],
                ['type' => 'number', 'value' => floatval( 4)],
                ['type' => 'rightsquarebracket', 'value' => ']'],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'leftsquarebracket', 'value' => '['],
                ['type' => 'number', 'value' => floatval( 5)],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'minus', 'value' => '-'],
                ['type' => 'number', 'value' => floatval( 6)],
                ['type' => 'rightsquarebracket', 'value' => ']'],
                ['type' => 'rightcurlybrace', 'value' => '}'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['B:={C,D}',[

                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'tobeequal', 'value' => ':='],
                ['type' => 'leftcurlybrace', 'value' => '{'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'rightcurlybrace', 'value' => '}'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['PointSetDiagram(A,B)',[

                ['type' => 'pointsetdiagram', 'value' => 'PointSetDiagram'],
                ['type' => 'leftparenthesis', 'value' => '('],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'rightparenthesis', 'value' => ')'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['PointSetDiagram({[3,5],[-7,-8]})',[

                ['type' => 'pointsetdiagram', 'value' => 'PointSetDiagram'],
                ['type' => 'leftparenthesis', 'value' => '('],
                ['type' => 'leftcurlybrace', 'value' => '{'],
                ['type' => 'leftsquarebracket', 'value' => '['],
                ['type' => 'number', 'value' => floatval( 3)],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'number', 'value' => floatval( 5)],
                ['type' => 'rightsquarebracket', 'value' => ']'],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'leftsquarebracket', 'value' => '['],
                ['type' => 'minus', 'value' => '-'],
                ['type' => 'number', 'value' => floatval( 7)],
                ['type' => 'comma', 'value' => ','],
                ['type' => 'minus', 'value' => '-'],
                ['type' => 'number', 'value' => floatval( 8)],
                ['type' => 'rightsquarebracket', 'value' => ']'],
                ['type' => 'rightcurlybrace', 'value' => '}'],
                ['type' => 'rightparenthesis', 'value' => ')'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['|H|',[

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'H'],
                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'eol', 'value' => '$']
            ]],
            ['|C|',[

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['(A∩C)∁=A∁∪C∁',[

                ['type' => 'leftparenthesis', 'value' => '('],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'rightparenthesis', 'value' => ')'],
                ['type' => 'complement', 'value' => '&comp;'],
                ['type' => 'equal', 'value' => '='],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'complement', 'value' => '&comp;'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'complement', 'value' => '&comp;'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['(A∪C)∁=A∁∩C∁',[

                ['type' => 'leftparenthesis', 'value' => '('],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'rightparenthesis', 'value' => ')'],
                ['type' => 'complement', 'value' => '&comp;'],
                ['type' => 'equal', 'value' => '='],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'complement', 'value' => '&comp;'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'complement', 'value' => '&comp;'],
                ['type' => 'eol', 'value' => '$']
           ]],
            ['|A|+|B|+|C|+|D|-|A∩B|-|A∩C|-|A∩D|-|B∩C|-|B∩D|-|C∩D|+|A∩B∩C|+|A∩B∩D|+|B∩C∩D|+|A∩C∩D|-|A∩ B∩C∩D|+|(A∪B∪C∪D)∁|',[

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'plus', 'value' => '+'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'plus', 'value' => '+'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'plus', 'value' => '+'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'minus', 'value' => '-'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'minus', 'value' => '-'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'minus', 'value' => '-'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'minus', 'value' => '-'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'minus', 'value' => '-'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'minus', 'value' => '-'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'plus', 'value' => '+'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'plus', 'value' => '+'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'plus', 'value' => '+'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'plus', 'value' => '+'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'minus', 'value' => '-'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'intersection', 'value' => '&cap;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'plus', 'value' => '+'],

                ['type' => 'verticalline', 'value' => '|'],
                ['type' => 'leftparenthesis', 'value' => '('],
                ['type' => 'identifier', 'value' => 'A'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'B'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'C'],
                ['type' => 'union', 'value' => '&cup;'],
                ['type' => 'identifier', 'value' => 'D'],
                ['type' => 'rightparenthesis', 'value' => ')'],
                ['type' => 'complement', 'value' => '&comp;'],
                ['type' => 'verticalline', 'value' => '|'],

                ['type' => 'eol', 'value' => '$']
            ]],
            ['A?B','Last good token is: {"type":"identifier","value":"A"} Position:2']
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
           $replaced=$test[0];
           foreach ($htmlEntityMap as $key => $value) {
                $replaced=str_replace($key,$value,$replaced);
            }
    
            $test[0]=$replaced;
        }
        return $tests;
    }   
}