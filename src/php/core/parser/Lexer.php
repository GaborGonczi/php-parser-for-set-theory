<?php
namespace core\parser;

use \core\Regexp;
use \core\lib\Builtin;

class Lexer
{

    private $input;
    private $specialChars=["&isin;",	"&notin;",	"&sube;",	
    "&sub;",	"&comp;",	"&cup;",	
    "&cap;",	"&and;",	"&or;",	
    "&setminus;",	"&mid;",	"&nmid;","&lt;","&gt;"];
    public function __construct($input = "")
    {
        $this->input = $input . "$";
    }
    public function tokenize()
    {

        $tokens = [];


        $numregexp = new RegExp(Token::NUMBER['value']);
        $idregexp = new RegExp(Token::IDENTIFIER['value']);
        $tobeeqregexp = new RegExp(Token::TOBEEQUAL['value']);
        $lessthanoreqregexp = new RegExp(Token::LESSTHANOREQUAL['value']);
        $greaterthanoreqregexp = new RegExp(Token::GREATERTHANOREQUAL['value']);


        for ($i = 0; $i < strlen($this->input); $i++) {
            
            if($this->input[$i]=='&'){
                $pos=strpos($this->input,';',$i);
                $specialchar=substr($this->input,$i,($pos-$i+1));
                if(in_array($specialchar,$this->specialChars)){
                    $c=html_entity_decode($specialchar,ENT_HTML5,"UTF-8");
                }
                $i=$pos;
            }
            else{
                $c=$this->input[$i];
            }
            if ($numregexp->test($c)) {
                $num = $c;
                while ($numregexp->test($num)) {
                    $num .= $this->input[++$i];
                }
                $tokens[] = ['type' => Token::NUMBER['name'], 'value' => floatval(substr($num, 0, -1))];
                --$i;
            } elseif ($idregexp->test($c)) {
                $id = $c;
                while ($idregexp->test($id)) {
                    $id .= $this->input[++$i];
                }
                $id = substr($id, 0, -1);
                if (in_array($id, Builtin::NAMES)) {
                    $tokens[] = ['type' => Builtin::TYPE, 'value' => $id];
                } else {
                    $tokens[] = ['type' => Token::IDENTIFIER["name"], 'value' => $id];
                }
                --$i;
            } elseif (isset($this->input[$i])&&isset($this->input[$i + 1])&&$tobeeqregexp->test($this->input[$i] . $this->input[$i + 1])) {
                $tokens[] = ['type' => Token::TOBEEQUAL['name'], 'value' => Token::TOBEEQUAL['value']];
                ++$i;
            } elseif (isset($this->input[$i])&&isset($this->input[$i + 1])&&$lessthanoreqregexp->test($this->input[$i] . $this->input[$i + 1])) {
                $tokens[] = ['type' => Token::LESSTHANOREQUAL['name'], 'value' => Token::LESSTHANOREQUAL['value']];
                ++$i;
            } elseif (isset($this->input[$i])&&isset($this->input[$i + 1])&&$greaterthanoreqregexp->test($this->input[$i] . $this->input[$i + 1])) {
                $tokens[] = ['type' => Token::GREATERTHANOREQUAL['name'], 'value' => Token::GREATERTHANOREQUAL['value']];
                ++$i;
            } elseif ($c === ' ' || $c === '\t') {
                continue;
            } else {
                switch ($c) {
                    case (TOKEN::PLUS['value']):
                        $tokens[] = ['type' => token::PLUS['name'], 'value' => TOKEN::PLUS['value']];
                        break;
                    case (TOKEN::MINUS['value']):
                        $tokens[] = ['type' => token::MINUS['name'], 'value' => TOKEN::MINUS['value']];
                        break;
                    case (TOKEN::MULTIPLY['value']):
                        $tokens[] = ['type' => token::MULTIPLY['name'], 'value' => TOKEN::MULTIPLY['value']];
                        break;
                    case (TOKEN::DIVIDE['value']):
                        $tokens[] = ['type' => token::DIVIDE['name'], 'value' => TOKEN::DIVIDE['value']];
                        break;
                    case (TOKEN::DOT['value']):
                        $tokens[] = ['type' => token::DOT['name'], 'value' => TOKEN::DOT['value']];
                        break;
                    case (TOKEN::COMPLEMENT['value']):
                        $tokens[] = ['type' => token::COMPLEMENT['name'], 'value' => TOKEN::COMPLEMENT['value']];
                        break;
                    case (TOKEN::ELEMENTOF['value']):
                        $tokens[] = ['type' => token::ELEMENTOF['name'], 'value' => TOKEN::ELEMENTOF['value']];
                        break;
                    case (TOKEN::NOTELEMENTOF['value']):
                        $tokens[] = ['type' => token::NOTELEMENTOF['name'], 'value' => TOKEN::NOTELEMENTOF['value']];
                        break;
                    case (TOKEN::EQUAL['value']):
                        $tokens[] = ['type' => TOKEN::EQUAL['name'], 'value' => TOKEN::EQUAL['value']];
                        break;
                    case (TOKEN::SUBSETOF['value']):
                        $tokens[] = ['type' => TOKEN::SUBSETOF['name'], 'value' => TOKEN::SUBSETOF['value']];
                        break;
                    case (TOKEN::REALSUBSETOF['value']):
                        $tokens[] = ['type' => TOKEN::REALSUBSETOF['name'], 'value' => TOKEN::REALSUBSETOF['value']];
                        break;
                    case (TOKEN::UNION['value']):
                        $tokens[] = ['type' => TOKEN::UNION['name'], 'value' => TOKEN::UNION['value']];
                        break;
                    case (TOKEN::INTERSECTION['value']):
                        $tokens[] = ['type' => TOKEN::INTERSECTION['name'], 'value' => TOKEN::INTERSECTION['value']];
                        break;
                    case (TOKEN::SETMINUS['value']):
                        $tokens[] = ['type' => TOKEN::SETMINUS['name'], 'value' => TOKEN::SETMINUS['value']];
                        break;
                    case (TOKEN::COMMA['value']):
                        $tokens[] = ['type' => TOKEN::COMMA['name'], 'value' => TOKEN::COMMA['value']];
                        break;
                    case (TOKEN::DIVIDES['value']):
                        $tokens[] = ['type' => TOKEN::DIVIDES['name'], 'value' => TOKEN::DIVIDES['value']];
                        break;
                    case (TOKEN::DOESNOTDIVIDE['value']):
                        $tokens[] = ['type' => TOKEN::DOESNOTDIVIDE['name'], 'value' => TOKEN::DOESNOTDIVIDE['value']];
                        break;
                    case (TOKEN::LAND['value']):
                        $tokens[] = ['type' => TOKEN::LAND['name'], 'value' => TOKEN::LAND['value']];
                        break;
                    case (TOKEN::LOR['value']):
                        $tokens[] = ['type' => TOKEN::LOR['name'], 'value' => TOKEN::LOR['value']];
                        break;
                    case (TOKEN::VERTICALLINE['value']):
                        $tokens[] = ['type' => TOKEN::VERTICALLINE['name'], 'value' => TOKEN::VERTICALLINE['value']];
                        break;
                    case (TOKEN::LEFTCURLYBRACE['value']):
                        $tokens[] = ['type' => TOKEN::LEFTCURLYBRACE['name'], 'value' => TOKEN::LEFTCURLYBRACE['value']];
                        break;
                    case (TOKEN::RIGHTCURLYBRACE['value']):
                        $tokens[] = ['type' => TOKEN::RIGHTCURLYBRACE['name'], 'value' => TOKEN::RIGHTCURLYBRACE['value']];
                        break;
                    case (TOKEN::LEFTPARENTHESIS['value']):
                        $tokens[] = ['type' => TOKEN::LEFTPARENTHESIS['name'], 'value' => TOKEN::LEFTPARENTHESIS['value']];
                        break;
                    case (TOKEN::RIGHTPARENTHESIS['value']):
                        $tokens[] = ['type' => TOKEN::RIGHTPARENTHESIS['name'], 'value' => TOKEN::RIGHTPARENTHESIS['value']];
                        break;
                    case (TOKEN::LEFTSQUAREBRACKET['value']):
                        $tokens[] = ['type' => TOKEN::LEFTSQUAREBRACKET['name'], 'value' => TOKEN::LEFTSQUAREBRACKET['value']];
                        break;
                    case (TOKEN::RIGHTSQUAREBRACKET['value']):
                        $tokens[] = ['type' => TOKEN::RIGHTSQUAREBRACKET['name'], 'value' => TOKEN::RIGHTSQUAREBRACKET['value']];
                        break;
                    case (TOKEN::LESSTHAN['value']):
                        $tokens[] = ['type' => TOKEN::LESSTHAN['name'], 'value' => TOKEN::LESSTHAN['value']];
                        break;
                    case (TOKEN::GREATERTHAN['value']):
                        $tokens[] = ['type' => TOKEN::GREATERTHAN['name'], 'value' => TOKEN::GREATERTHAN['value']];
                        break;
                    case (TOKEN::EOL['value']):
                        $tokens[] = ['type' => TOKEN::EOL['name'], 'value' => TOKEN::EOL['value']];
                        break;   
                    default:
                        $lastGood = $tokens[count($tokens) - 1];
                        $tokens[] = ['type' => 'undefined', 'value' => 'Last good: ' . print_r($lastGood,true), 'rowPos' => $i + 1];
                        return $tokens;
                }
            }
        }

        return $tokens;
    }
}