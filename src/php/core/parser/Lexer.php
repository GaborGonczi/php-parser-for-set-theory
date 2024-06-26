<?php
namespace core\parser;

use \core\Regexp;
use \core\lib\Builtin;
use \core\parser\exception\LexerException;
use \utils\Lang;

/**
* A class that represents a lexer for the set theory language.
* A lexer is a component that converts a sequence of characters into a sequence of tokens.
* A token is a meaningful unit of the language, such as a keyword, an identifier, an operator, etc.
*
* @package core\parser
*/
class Lexer
{

    /**
    * @var string A string that contains the input to be lexed
    */
    private $input;

    /**
    * An array that contains the special characters that are used in the language
    */
    private $specialChars=["&isin;",	"&notin;",	"&sube;",	
    "&sub;",	"&comp;",	"&cup;",	
    "&cap;",	"&and;",	"&or;",	
    "&setminus;",	"&mid;",	"&nmid;","&lt;","&gt;"];

    private $dev;
    private static $lang;

    /**
    * The constructor of the lexer class.
    * @param string $input The input to be lexed. Default is an empty string.
    */
    public function __construct($input = "",$dev=false,$lang='hun')
    {
        $this->dev=$dev;
        self::$lang=$lang;
        $this->setInput($input);
    }

    /**
    * A method that sets the input to be lexed.
    * @param string $input The input to be lexed.
    */
    public function setInput($input)
    {
        $this->input= $input . "$";
    }

    public function getTokens(){
        try {
            $tokens=$this->tokenize();
        } catch (LexerException $le) {
            return (string) $le;
        }
        return $tokens;
    }

    public function setDevErrorMessages($dev=true){
        $this->dev=$dev;
    }

    /**
    * A method that tokenizes the input and returns an array of tokens.
    * @return array An array of tokens.
    * @throws LexerException If an invalid character is encountered.
    */
    private function tokenize()
    {

        $tokens = [];


        $numregexp = new Regexp(Token::NUMBER['value']);
        $idregexp = new Regexp(Token::IDENTIFIER['value']);
        $tobeeqregexp = new Regexp(Token::TOBEEQUAL['value']);
        $lessthanoreqregexp = new Regexp(Token::LESSTHANOREQUAL['value']);
        $greaterthanoreqregexp = new Regexp(Token::GREATERTHANOREQUAL['value']);
        $arrow = new Regexp(Token::ARROW['value']);


        for ($i = 0; $i < strlen($this->input); $i++) {
            
            if($this->input[$i]=='&'){
                $pos=strpos($this->input,';',$i);
                $specialchar=substr($this->input,$i,($pos-$i+1));
                if(in_array($specialchar,$this->specialChars)){
                    switch ($specialchar) {
                        case (TOKEN::COMPLEMENT['value']):
                            $tokens[] = ['type' => TOKEN::COMPLEMENT['name'], 'value' => TOKEN::COMPLEMENT['value']];
                            break;
                        case (TOKEN::ELEMENTOF['value']):
                            $tokens[] = ['type' => TOKEN::ELEMENTOF['name'], 'value' => TOKEN::ELEMENTOF['value']];
                            break;
                        case (TOKEN::NOTELEMENTOF['value']):
                            $tokens[] = ['type' => TOKEN::NOTELEMENTOF['name'], 'value' => TOKEN::NOTELEMENTOF['value']];
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
                    }
                }
                $i=$pos;
                continue;
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
                    $tokens[] = ['type' => strtolower($id), 'value' => $id];
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
            } else if (isset($this->input[$i])&&isset($this->input[$i + 1])&&$arrow->test($this->input[$i] . $this->input[$i + 1])) {
                $tokens[] = ['type' => Token::ARROW['name'], 'value' => Token::ARROW['value']];
                ++$i;
            }  elseif ($c === ' ' || $c === '\t') {
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
                    case (TOKEN::EQUAL['value']):
                        $tokens[] = ['type' => TOKEN::EQUAL['name'], 'value' => TOKEN::EQUAL['value']];
                        break;
                    case (TOKEN::COMMA['value']):
                        $tokens[] = ['type' => TOKEN::COMMA['name'], 'value' => TOKEN::COMMA['value']];
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
                        throw new LexerException($this->getErrorMessage($lastGood,$i));
                }
            }
        }

        return $tokens;
    }

    private function getErrorMessage($lastGood,$i) {
        if($this->dev){
            return Lang::getString('lexerErrorStart',self::$lang) . json_encode($lastGood). Lang::getString('lexerErrorColumn',self::$lang). $i+1;
        }
        else{
            return Lang::getString('lexerErrorStartUser',self::$lang) . $lastGood['value']. Lang::getString('lexerErrorColumnUser',self::$lang). $i+1;
        }
        
    }
}