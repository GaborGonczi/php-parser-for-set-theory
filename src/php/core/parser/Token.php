<?php
namespace core\parser;

/**
* A class that defines the constants for the tokens of the set theory language.
* A token is a meaningful unit of the language, such as a keyword, an identifier, an operator, etc.
* Each token has a name and a value, which are stored as associative arrays.
*
* @package core\parser
*/
class Token
{
    /**
    * @var array The token for the plus operator
    */
    const PLUS = ['name' => 'plus', 'value' => '+'];

    /**
    * @var array The token for the minus operator
    */
    const MINUS = ['name' => 'minus', 'value' => '-'];

    /**
    * @var array The token for the multiply operator
    */
    const MULTIPLY = ['name' => 'multiply', 'value' => '*'];

    /**
    * @var array The token for the divide operator
    */
    const DIVIDE = ['name' => 'divide', 'value' => '/'];

    /**
    * @var array The token for the dot operator
    */
    const DOT = ['name' => 'dot', 'value' => '.'];

    /**
    * @var array The token for the to-be-equal symbol
    */
    const TOBEEQUAL = ['name' => 'tobeequal', 'value' => ':='];
    
    /**
    * @var array The token for the element-of symbol
    */
    const ELEMENTOF = ['name' => 'elementof', 'value' => '&isin;'];

    /**
    * @var array The token for the not-element-of symbol
    */
    const NOTELEMENTOF = ['name' => 'notelementof', 'value' => '&notin;'];

    /**
    * @var array The token for the equal symbol
    */
    const EQUAL = ['name' => 'equal', 'value' => '='];

    /**
    * @var array The token for the subset-of symbol
    */
    const SUBSETOF = ['name' => 'subsetof', 'value' => '&sube;'];

    /**
    * @var array The token for the real-subset-of symbol
    */
    const REALSUBSETOF = ['name' => 'realsubsetof', 'value' => '&sub;'];

    /**
    * @var array The token for the complement symbol
    */
    const COMPLEMENT = ['name' => 'complement', 'value' => '&comp;'];

    /**
    * @var array The token for the union symbol
    */
    const UNION = ['name' => 'union', 'value' => '&cup;'];

    /**
    * @var array The token for the intersection symbol
    */
    const INTERSECTION = ['name' => 'intersection', 'value' => '&cap;'];

    /**
    * @var array The token for the setminus symbol
    */
    const SETMINUS = ['name' => 'setminus', 'value' => '&setminus;'];

    /**
    * @var array The token for the comma symbol
    */
    const COMMA = ['name' => 'comma', 'value' => ','];

    /**
    * @var array The token for the divides symbol
    */
    const DIVIDES = ['name' => 'divides', 'value' => '&mid;'];

    /**
    * @var array The token for the does-not-divide symbol
    */
    const DOESNOTDIVIDE = ['name' => 'doesnotdivide', 'value' => '&nmid;'];

    /**
    * @var array The token for the verticalline symbol
    */
    const VERTICALLINE = ['name' => 'verticalline', 'value' => '|'];

    /**
    * @var array The token for the logical-and symbol
    */
    const LAND = ['name' => 'land', 'value' => '&and;'];

    /**
    * @var array The token for the logical-or symbol
    */
    const LOR = ['name' => 'lor', 'value' => '&or;'];

    /**
    * @var array The token for the leftcurlybrace symbol
    */
    const LEFTCURLYBRACE = ['name' => 'leftcurlybrace', 'value' => '{'];
    
    /**
    * @var array The token for the rightcurlybrace symbol
    */
    const RIGHTCURLYBRACE = ['name' => 'rightcurlybrace', 'value' => '}'];

    /**
    * @var array The token for the leftparenthesis symbol
    */
    const LEFTPARENTHESIS = ['name' => 'leftparenthesis', 'value' => '('];

    /**
    * @var array The token for the rightparenthesis symbol
    */
    const RIGHTPARENTHESIS = ['name' => 'rightparenthesis', 'value' => ')'];

    /**
    * @var array The token for the leftsquarebracket symbol
    */
    const LEFTSQUAREBRACKET = ['name' => 'leftsquarebracket', 'value' => '['];

    /**
    * @var array The token for the rightsquarebracket symbol
    */
    const RIGHTSQUAREBRACKET = ['name' => 'rightsquarebracket', 'value' => ']'];

    /**
    * @var array The token for the lessthan symbol
    */
    const LESSTHAN = ['name' => 'lessthan', 'value' => '<'];

    /**
    * @var array The token for greaterthan symbol
    */
    const GREATERTHAN = ['name' => 'greaterthan', 'value' => '>'];

    /**
    * @var array The token for the lessthan-or-equal symbol
    */
    const LESSTHANOREQUAL = ['name' => 'lessthanorequal', 'value' => '<='];

    /**
    * @var array The token for the greaterthan-or-equal symbol
    */
    const GREATERTHANOREQUAL = ['name' => 'greaterthanorequal', 'value' => '>='];

    /**
    * @var array The token for the arrow symbol
    */
    const ARROW=['name' =>'arrow', 'value' => '->'];

    /**
    * @var array The token for the identifierd
    */
    const IDENTIFIER = ['name' => 'identifier', 'value' => '^([_a-zA-Z][_a-zA-Z0-9]*)$'];

    /**
    * @var array The token for the numbers
    */
    const NUMBER = ['name' => 'number', 'value' => '^(0|[1-9][0-9]*)$'];

    /**
    * @var array The token for the eol symbol
    */
    const EOL = ['name' => 'eol', 'value' => '$'];
    
    /*tokens for reserved  words */

    /**
    * @var array The token for the Venn keyword
    */
    const VENN =['name'=>'venn', 'value'=>'Venn'];

    /**
    * @var array The token for the PointSetDiagram keyword
    */
    const POINTSETDIAGRAM=['name'=>'pointsetdiagram','value'=>'PointSetDiagram'];

    /**
    * @var array The token for the add keyword
    */
    const ADD=['name'=>'add','value'=>'add'];

    /**
    * @var array The token for the delete keyword
    */
    const DELETE=['name'=>'delete','value'=>'delete'];
}