<?php

use \PHPUnit\Framework\TestCase;
use \core\parser\Token;

class TokenTest extends TestCase {

    /**
    * @dataProvider constantProvider
    */
    public function testConstants($constant, $name, $value) {

        $this->assertIsArray($constant);
        $this->assertArrayHasKey('name', $constant);
        $this->assertArrayHasKey('value', $constant);

        $this->assertEquals($name, $constant['name']);
        $this->assertEquals($value, $constant['value']);
    }
    
    public static function constantProvider() {
        return [
        
            [Token::PLUS, 'plus', '+'],
            [Token::MINUS, 'minus', '-'],
            [Token::MULTIPLY, 'multiply', '*'],
            [Token::DIVIDE, 'divide', '/'],
            [Token::DOT, 'dot', '.'],
            [Token::TOBEEQUAL, 'tobeequal', ':='],
            [Token::ELEMENTOF, 'elementof', '∈'],
            [Token::NOTELEMENTOF, 'notelementof', '∉'],
            [Token::EQUAL, 'equal', '='],
            [Token::SUBSETOF, 'subsetof', '⊆'],
            [Token::REALSUBSETOF, 'realsubsetof', '⊂'],
            [Token::COMPLEMENT, 'complement', '∁'],
            [Token::UNION, 'union', '∪'],
            [Token::INTERSECTION, 'intersection', '∩'],
            [Token::SETMINUS, 'setminus', '∖'],
            [Token::COMMA, 'comma', ','],
            [Token::DIVIDES, 'divides', '∣'],
            [Token::DOESNOTDIVIDE, 'doesnotdivide', '∤'],
            [Token::VERTICALLINE, 'verticalline', '|'],
            [Token::LAND, 'land', '∧'],
            [Token::LOR, 'lor', '∨'],
            [Token::LEFTCURLYBRACE, 'leftcurlybrace', '{'],
            [Token::RIGHTCURLYBRACE, 'rightcurlybrace', '}'],
            [Token::LEFTPARENTHESIS, 'leftparenthesis', '('],
            [Token::RIGHTPARENTHESIS, 'rightparenthesis', ')'],
            [Token::LEFTSQUAREBRACKET, 'leftsquarebracket', '['],
            [Token::RIGHTSQUAREBRACKET, 'rightsquarebracket', ']'],
            [Token::LESSTHAN, 'lessthan', '<'],
            [Token::GREATERTHAN, 'greaterthan', '>'],
            [Token::LESSTHANOREQUAL, 'lessthanorequal', '<='],
            [Token::GREATERTHANOREQUAL, 'greaterthanorequal', '>='],
            [Token::IDENTIFIER, 'identifier', '^([_a-zA-Z][_a-zA-Z0-9]*)$'],
            [Token::NUMBER, 'number', '^(0|[1-9][0-9]*)$'],

            [Token::VENN, 'venn', 'Venn'],
            [Token::POINTSETDIAGRAM, 'pointsetdiagram', 'PointSetDiagram'],
            [Token::ADD, 'add', 'add'],
            [Token::DELETE, 'delete', 'delete']
        ];
    }
}