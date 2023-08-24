<?php
namespace core\parser;

class Token
{
    const PLUS = ['name' => 'plus', 'value' => '+'];
    const MINUS = ['name' => 'minus', 'value' => '-'];
    const MULTIPLY = ['name' => 'multiply', 'value' => '*'];
    const DIVIDE = ['name' => 'divide', 'value' => '/'];
    const DOT = ['name' => 'dot', 'value' => '.'];
    const TOBEEQUAL = ['name' => 'tobeequal', 'value' => ':='];
    const ELEMENTOF = ['name' => 'elementof', 'value' => '∈'];
    const NOTELEMENTOF = ['name' => 'notelementof', 'value' => '∉'];
    const EQUAL = ['name' => 'equal', 'value' => '='];
    const SUBSETOF = ['name' => 'subsetof', 'value' => '⊆'];
    const REALSUBSETOF = ['name' => 'realsubsetof', 'value' => '⊂'];
    const COMPLEMENT = ['name' => 'complement', 'value' => '∁'];
    const UNION = ['name' => 'union', 'value' => '∪'];
    const INTERSECTION = ['name' => 'intersection', 'value' => '∩'];
    const SETMINUS = ['name' => 'setminus', 'value' => '∖'];
    const COMMA = ['name' => 'comma', 'value' => ','];
    const DIVIDES = ['name' => 'divides', 'value' => '∣'];
    const DOESNOTDIVIDE = ['name' => 'doesnotdivide', 'value' => '∤'];
    const VERTICALLINE = ['name' => 'verticalline', 'value' => '|'];
    const LAND = ['name' => 'land', 'value' => '∧'];
    const LOR = ['name' => 'lor', 'value' => '∨'];
    const LEFTCURLYBRACE = ['name' => 'leftcurlybrace', 'value' => '{'];
    const RIGHTCURLYBRACE = ['name' => 'rightcurlybrace', 'value' => '}'];
    const LEFTPARENTHESIS = ['name' => 'leftparenthesis', 'value' => '('];
    const RIGHTPARENTHESIS = ['name' => 'rightparenthesis', 'value' => ')'];
    const LEFTSQUAREBRACKET = ['name' => 'leftsquarebracket', 'value' => '['];
    const RIGHTSQUAREBRACKET = ['name' => 'rightsquarebracket', 'value' => ']'];
    const LESSTHAN = ['name' => 'lessthan', 'value' => '<'];
    const GREATERTHAN = ['name' => 'greaterthan', 'value' => '>'];
    const LESSTHANOREQUAL = ['name' => 'lessthanorequal', 'value' => '<='];
    const GREATERTHANOREQUAL = ['name' => 'greaterthanorequal', 'value' => '>='];
    const IDENTIFIER = ['name' => 'identifier', 'value' => '^([_a-zA-Z][_a-zA-Z0-9]*)$'];
    const NUMBER = ['name' => 'number', 'value' => '^(0|[1-9][0-9]*)$'];
    const EOL = ['name' => 'eol', 'value' => '$'];
}