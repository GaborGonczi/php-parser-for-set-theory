<?php
namespace core\parser;

use \ReflectionFunction;

use \core\lib\Map;
use \core\lib\Functions;
use core\lib\PointSetDiagramFunctions;
use \core\lib\Point as LPoint;
use core\parser\exception\ParserException;

class Parser
{

    private $tokens;
    private $pos;
    private Map $vars;



    public function parse()
    {
        try {
            $result = $this->statement();
            $this->match('$');
        } catch (ParserException $pe) {
            return $this->getStringRepresentation($pe);
        }
        return $this->getStringRepresentation($result);
    }
    private function statement()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (TOKEN::MINUS['name']):
            case (TOKEN::NUMBER['name']):
                $result = $this->selementofnelementof();
                break;
            case (TOKEN::LEFTCURLYBRACE['name']):
            case (TOKEN::LEFTSQUAREBRACKET['name']):
            case (TOKEN::IDENTIFIER['name']):
                $result = $this->sexpr();
                break;
            case (Token::VERTICALLINE['name']):
                $result = $this->ssimpleexpression();
                break;
            case (Token::VENN['name']):
            case (Token::POINTSETDIAGRAM['name']):
                $result=$this->sfunctioncall();
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
           
        }
        return $result;
    }
    private function selementofnelementof()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (TOKEN::MINUS['name']):
            case (TOKEN::NUMBER['name']):
                $num = $this->wholenumber();
                $operationandset = $this->selementofnelementof_();
                if ($operationandset['op'] === Token::ELEMENTOF['value'])
                    $result = Functions::isElementOf($num, $operationandset['set']);
                else if ($operationandset['op'] === Token::NOTELEMENTOF['value'])
                    $result = Functions::isNotElementOf($num, $operationandset['set']);


                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
           
        }
        return $result;
    }
    private function wholenumber()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (TOKEN::MINUS['name']):
                $this->match("-");
                $lookahead = $this->lookahead();
                $this->match($lookahead['value']);
                $result = -1 * $lookahead['value'];
                break;
            case (TOKEN::NUMBER['name']):
                $this->match($lookahead['value']);
                $result = $lookahead['value'];
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
    
        }
        return $result;
    }
    private function selementofnelementof_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (TOKEN::ELEMENTOF['name']):
                $this->match('∈');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->setoperationside();
                break;
            case (TOKEN::NOTELEMENTOF['name']):
                $this->match('∉');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->setoperationside();
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
       
        }
        return $result;
    }
    private function setoperationside()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::LEFTCURLYBRACE['name']):
                $result = $this->curliedsetexp();
                break;
            case (Token::IDENTIFIER['name']):
                $result = $this->vars->get($lookahead['value']);
                if ($result == null) {
                    $result = $lookahead['value'];
                }
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
               
        }
        return $result;
    }
    private function curliedsetexp()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::LEFTCURLYBRACE['name']):
                $this->match('{');
                $result = $this->setexp();
                $this->match('}');
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
      
        }
        return $result;
    }
    private function setexp()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
                $setliteral = $this->setliteral();
                $result = Functions::createSetFromArray($setliteral);
                break;
            case (Token::LEFTSQUAREBRACKET['name']):
                $pointsetliteral = $this->pointsetliteral();
                $result = Functions::createSetFromArray($pointsetliteral);
                break;
            case (Token::IDENTIFIER['name']):
                $setformula = $this->setformula();
                $result = Functions::createSetFromFormula($setformula['start'],$setformula['end'],$setformula['boundformula']);
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
                
        }

        return $result;
    }
    private function setformula()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::IDENTIFIER['name']):
                $this->match($lookahead['value']);
                $lookahead = $this->lookahead();
                $this->match('|');
                $logicalexp = $this->logicalexp();
                $bounds=Functions::collectBounds($logicalexp);
                $bounds=Functions::getMinMax($bounds);
                $boundformula=Functions::processConditionTree($logicalexp);
                $result=['start'=>$bounds['start'],'end'=>$bounds['end'],'boundformula'=>$boundformula];
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
         
        }
        return $result;
    }
    private function logicalexp()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
            case (Token::IDENTIFIER['name']):
            case (Token::LEFTPARENTHESIS['name']):
                $subexp = $this->logicalsubexp();
                $rest = $this->logicalexp_();
                $merged = array_merge_recursive(['subexp' => $subexp], [...$rest]);
                $result=Functions::transformConditionsToTree($merged);
                
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
            
        }
        return $result;
    }
    private function logicalsubexp()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
                $num = $this->wholenumber();
                $divop = $this->divisibilityoperator();
                $lookahead = $this->lookahead();
                $this->match($lookahead['value']);
                $identifier = $lookahead['value'];
                $result[$identifier][] = Functions::createDivisibilityCondition($num, $divop);
                break;
            case (Token::IDENTIFIER['name']):
                $this->match($lookahead['value']);
                $identifier = $lookahead['value'];
                $comparsionop = $this->comparsionoperator();
                $logicalrhs = $this->logicalrhs();
                $result[$identifier][] = Functions::createComparsionCondition($comparsionop, Functions::processLogicalRhs($logicalrhs));
                $result[$identifier]["bound"]=$logicalrhs['num'];
                break;
            case (Token::LEFTPARENTHESIS['name']):
                $this->match('(');
                $logicalexp = $this->logicalexp();
                $lookahead = $this->lookahead();
                $this->match(')');
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
      
        }
        return $result;
    }
    private function divisibilityoperator()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::DIVIDES['name']):
                $this->match('∣');
                $result = $lookahead['value'];
                break;
            case (Token::DOESNOTDIVIDE['name']):
                $this->match('∤');
                $result = $lookahead['value'];
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
   
        }


        return $result;
    }
    private function comparsionoperator()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::EQUAL['name']):
                $this->match('=');
                $result = $lookahead['value'];
                break;
            case (Token::LESSTHAN['name']):
                $this->match('<');
                $result = $lookahead['value'];
                break;
            case (Token::GREATERTHAN['name']):
                $this->match('>');
                $result = $lookahead['value'];
                break;
            case (Token::LESSTHANOREQUAL['name']):
                $this->match('<=');
                $result = $lookahead['value'];
                break;
            case (Token::GREATERTHANOREQUAL['name']):
                $this->match('>=');
                $result = $lookahead['value'];
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
           
        }
        return $result;
    }
    private function logicalrhs()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
                $num = $this->wholenumber();
                $rest = $this->logicalrhs_();
                $result = array_merge_recursive(['num' => $num], [...$rest]);
                break;
            case (Token::IDENTIFIER['name']):
                $this->match($lookahead['value']);
                $identifier = $lookahead['value'];
                $rest = $this->logicalrhs_();
                $result[$identifier][] = [...$rest];
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
            
        }


        return $result;
    }
    private function logicalrhs_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::PLUS['name']):
            case (Token::MINUS['name']):
            case (Token::MULTIPLY['name']):
            case (Token::DIVIDE['name']):
                $simpleop = $this->simpleoperator();
                $rest = $this->logicalrhs__();
                $result = ['simpleop' => $simpleop, ...$rest];
                break;
            case (Token::LAND['name']):
            case (Token::LOR['name']):
            case (Token::RIGHTCURLYBRACE['name']):
            case (Token::RIGHTPARENTHESIS['name']):
                $result = [];
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
                
        }
        return $result;
    }
    private function logicalrhs__()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
                $result['number'] = $this->wholenumber();
                break;
            case (Token::IDENTIFIER['name']):
                $this->match($lookahead['value']);
                $result['id'] = $lookahead['value'];
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
             
        }
        return $result;
    }
    private function logicalexp_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::LAND['name']):
            case (Token::LOR['name']):
                $logicalop = $this->logicaloperator();
                $rest = $this->logicalexp();
                $result = array_merge_recursive(['logicalop' => $logicalop], ["subexp"=>[...$rest]]);
                break;
            case (Token::RIGHTCURLYBRACE['name']):
            case (Token::RIGHTPARENTHESIS['name']):
                $result = [];
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
                
        }
        return $result;
    }
    private function logicaloperator()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::LAND['name']):
                $this->match('∧');
                $result = $lookahead['value'];
                break;
            case (Token::LOR['name']):
                $this->match('∨');
                $result = $lookahead['value'];
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
             
        }
        return $result;
    }
    private function setliteral()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
                $num = $this->wholenumber();
                $rest = $this->setliteral_();
                $result = Functions::removeNullFromArray([$num, ...$rest]);
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
           
        }
        return $result;
    }
    private function setliteral_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::COMMA['name']):
                $this->match(',');
                $result = $this->setliteral();
                break;
            case (Token::RIGHTCURLYBRACE['name']):
                $result = [];
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
           
        }
        return $result;
    }
    private function pointsetliteral()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::LEFTSQUAREBRACKET['name']):
                $point = $this->point();
                $rest = $this->pointsetliteral_();
                $result = Functions::removeNullFromArray([$point, ...$rest]);
                break;
            case (Token::RIGHTCURLYBRACE['name']):
                $result = [];
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
              
        }
        return $result;
    }
    private function pointsetliteral_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::COMMA['name']):
                $this->match(',');
                $result = $this->pointsetliteral();
                break;
            case (Token::RIGHTCURLYBRACE['name']):
                $result = [];
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
            
        }
        return $result;
    }
    private function sexpr()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (TOKEN::LEFTCURLYBRACE['name']):
            case (TOKEN::IDENTIFIER['name']):
                $setoperationside = $this->setoperationside();
                $rest = $this->sexpr_();
                if (isset($rest['op'])) {
                    switch ($rest['op']) {
                        case (Token::TOBEEQUAL['name']):
                            if (gettype($setoperationside) === "string") {
                                $this->vars->add($setoperationside, $rest['set']);
                            }
                            break;
                        case (Token::EQUAL['name']):
                            $result = Functions::areEqual($setoperationside, $rest['set']);
                            break;
                        case (Token::SUBSETOF['name']):
                            $result = Functions::isSubsetOf($setoperationside, $rest['set']);
                            break;
                        case (Token::REALSUBSETOF['name']):
                            $result = Functions::isRealSubsetOf($setoperationside, $rest['set']);
                            break;
                        case (Token::COMPLEMENT['name']):
                            $result = Functions::complement($setoperationside, $rest['set']);
                            break;
                        case (Token::UNION['name']):
                            $result = Functions::union($setoperationside, $rest['set']);
                            break;
                        case (Token::INTERSECTION['name']):
                            $result = Functions::intersection($setoperationside, $rest['set']);
                            break;
                        case (Token::SETMINUS['name']):
                            $result = Functions::difference($setoperationside, $rest['set']);
                            break;
                        default:
                            $pos = $this->calculatePosition();
                           throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
                            
                    }

                } else if ($rest === false) {
                    $result = $setoperationside;
                }

                break;

            case (Token::LEFTSQUAREBRACKET['name']):
                $result = $this->point();
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
               
        }
        return $result;
    }
    private function sexpr_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::TOBEEQUAL['name']):
            case (Token::EQUAL['name']):
            case (Token::SUBSETOF['name']):
            case (Token::REALSUBSETOF['name']):
            case (Token::COMPLEMENT['name']):
            case (Token::UNION['name']):
            case (Token::INTERSECTION['name']):
            case (Token::SETMINUS['name']):
                $result = $this->stesruisc();
                break;
            case (Token::VERTICALLINE['name']):
            case (Token::EOL['name']):
                $result = false;
                break;
            case (Token::IDENTIFIER['name']):
                $result = $this->sofunctioncall();
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
              
        }
        return $result;
    }
    private function stesruisc()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {

            case (Token::TOBEEQUAL['name']):
                $this->match(':=');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->curliedsetexp();
                break;

            case (Token::EQUAL['name']):
                $this->match('=');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->curliedsetexp();
                break;

            case (Token::SUBSETOF['name']):
                $this->match('⊆');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->curliedsetexp();
                break;

            case (Token::REALSUBSETOF['name']):
                $this->match('⊂');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->curliedsetexp();
                break;

            case (Token::COMPLEMENT['name']):
                $this->match('∁');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->curliedsetexp();
                break;

            case (Token::UNION['name']):
                $this->match('∪');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->curliedsetexp();
                break;

            case (Token::INTERSECTION['name']):
                $this->match('∩');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->curliedsetexp();
                break;

            case (Token::SETMINUS['name']):
                $this->match('∖');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->curliedsetexp();
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
            
        }
        return $result;
    }
    private function sofunctioncall()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::IDENTIFIER['name']):
                $setname = $lookahead['value'];
                $this->match($lookahead['value']);
                $this->match('.');
                $funcname = $this->sofunctionname();
                $this->match('(');
                $arguments = $this->arguments();
                $this->match(')');
                if ($this->vars->has($setname)) {
                    $set = $this->vars->get($setname);
                    $funcname=$funcname.'Element';
                    $result = Functions::$funcname($arguments, $set);
                }


                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
             
        }
        return $result;

    }
    private function sofunctionname()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::ADD['name']):
                $result = $lookahead['value'];
                $this->match('add');
                break;
            case (Token::DELETE['name']):
                $result = $lookahead['value'];
                $this->match('delete');
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
            
        }
        return $result;
    }
    private function arguments()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
                $arg = $this->argument();
                $rest = $this->arguments_();
                $result = [$arg, ...$rest];
                break;
            case (Token::IDENTIFIER['name']):
                $arg = $this->argument();
                if($arg===null){
                    $pos = $this->calculatePosition();
                    throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
                }
                $rest = $this->arguments_();
                $result = [$arg, ...$rest];
                break;
            case (Token::LEFTCURLYBRACE['name']):
                $arg = $this->argument();
                $rest = $this->arguments_();
                $result = Functions::removeEmptyArrayFromArray([...$arg,$rest]);
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
           
        }
        return $result;
    }
    private function argument()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
                $result = $this->wholenumber();
                break;
            case (Token::LEFTCURLYBRACE['name']):
                $result = $this->curliedsetexp();
                break;

            case (Token::IDENTIFIER['name']):
                $this->match($lookahead['value']);
                $result = $this->vars->get($lookahead['value']);
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
               
        }
        return $result;
    }
    private function arguments_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::COMMA['name']):
                $this->match(',');
                $result = $this->argument();
                break;
            case (Token::RIGHTPARENTHESIS["name"]):
                $result = [];
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
                
        }
        return $result;
    }
    private function point()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::LEFTSQUAREBRACKET['name']):
                $this->match('[');
                $point_x = $this->wholenumber();
                $this->match(',');
                $point_y = $this->wholenumber();
                $this->match(']');
                $result = new LPoint($point_x, $point_y);
                break;

            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }
    private function ssimpleexpression()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::VERTICALLINE['name']):
                $cardinality = $this->scardinality();
                $rest = $this->ssimpleexpression_();
                if(isset($rest['op'])){
                    switch ($rest['op']) {
                        case (Token::PLUS['name']):
                            $result = $cardinality + $rest['scardinality'];
                            break;
                        case (Token::MINUS['name']):
                            $result = $cardinality - $rest['scardinality'];
                            break;
                        case (Token::MULTIPLY['name']):
                            $result = $cardinality * $rest['scardinality'];
                            break;
                        case (Token::DIVIDE['name']):
                            $result = $cardinality / $rest['scardinality'];
                            break;
                        default:
                            $pos = $this->calculatePosition();
                           throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
               
                    }
                }
                else if ($rest===false) {
                   $result=$cardinality;
                }
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;

    }
    private function scardinality()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::VERTICALLINE['name']):
                $this->match('|');
                $setoperationside = $this->setoperationside();
                $rest = $this->sexpr_();
                if (
                    isset($rest['set']) && isset($rest['op']) && in_array(
                        $rest['op'],
                        array(
                            Token::COMPLEMENT['value'],
                            Token::INTERSECTION['value'],
                            Token::UNION['value'],
                            Token::SETMINUS['value']
                        )
                    )
                ) {
                    switch ($rest['op']) {
                        case (Token::COMPLEMENT['value']):
                            $set = Functions::complement($setoperationside, $rest['set']);
                            break;
                        case (Token::INTERSECTION['value']):
                            $set = Functions::intersection($setoperationside, $rest['set']);
                            break;
                        case (Token::UNION['value']):
                            $set = Functions::union($setoperationside, $rest['set']);
                            break;
                        case (Token::SETMINUS['value']):
                            $set = Functions::difference($setoperationside, $rest['set']);
                            break;
                        default:
                            $pos = $this->calculatePosition();
                           throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
                      
                    }
                }
                else{
                    $set=$setoperationside;
                }
                $this->match('|');
                $result = Functions::cardinality($set);
                
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
           
        }
        return $result;
    }
    private function ssimpleexpression_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::PLUS['name']):
            case (Token::MINUS['name']):
            case (Token::MULTIPLY['name']):
            case (Token::DIVIDE['name']):
                $result['op'] = $this->simpleoperator();
                $result['cardinality'] = $this->scardinality();
                break;
            case (Token::EOL['name']):
                $result=false;
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
   
        }
        return $result;
    }
    private function simpleoperator()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::PLUS['name']):
                $this->match('+');
                $result = $lookahead['value'];
                break;
            case (Token::MINUS['name']):
                $this->match('-');
                $result = $lookahead['value'];
                break;
            case (Token::MULTIPLY['name']):
                $this->match('*');
                $result = $lookahead['value'];
                break;
            case (Token::DIVIDE['name']):
                $this->match('/');
                $result = $lookahead['value'];
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));

        }


        return $result;
    }
    private function sfunctioncall()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::VENN['name']):
            case (Token::POINTSETDIAGRAM['name']):
                $name = $this->sfunctionname();
                $this->match('(');
                $arguments = $this->arguments();
                $this->match(')');
                if($name==="Venn"){
                    $result = Functions::$name(...$arguments);
                }
                else if($name==="PointSetDiagram"){
                    $argumentSet=PointSetDiagramFunctions::createSetFromPointArray($arguments);
                    $result=PointSetDiagramFunctions::$name($argumentSet);
                }
                
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
         
        }
        return $result;
    }
    private function sfunctionname()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::POINTSETDIAGRAM['name']):
                $result = $lookahead['value'];
                $this->match('PointSetDiagram');
                break;
            case (Token::VENN['name']):
                $result = $lookahead['value'];
                $this->match('Venn');
                break;
            default:
                $pos = $this->calculatePosition();
               throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getPossibleTokens(__FUNCTION__)));
        
        }
        return $result;
    }

    public function __construct($tokens = [])
    {
        $this->tokens = $tokens;
        $this->pos = 0;
        $this->vars=new Map([]);
    }

    public function getVars()
    {
        return $this->vars;
    }

    private function getFunctionReturnType($funcname): string
    {
        $reflectionFunction = new ReflectionFunction($funcname);
        return (string) $reflectionFunction->getReturnType();
    }
    private function getReturnedValueAsReturTypeObject($result, $funcAsString)
    {
        return array_reduce(array_keys($result), function ($carry, $key) use ($result) {
            $carry->$key = $result[$key];
            return $carry;
        }, new $funcAsString());
    }
    private function getStringRepresentation($result)
    {
        if (is_null($result)) {
            return "null";
        } else if (is_bool($result)) {
            return $result ? "true" : "false";
        } else {
            return (string) $result;
        }
    }
    private function getErrorMessage($lookahead, $pos, $func,$expected)
    {
        return "Unexpected token $lookahead[type] ($lookahead[value]) at position ".($pos+1)." in func $func; excepted token is one of the following: ".json_encode($expected);
    }
    private function calculatePosition()
    {
        $pos = 0;
        for ($i = 0; $i < $this->pos; $i++) {
            $pos += strlen(strval($this->tokens[$i]['value']));
        }
        return $pos;
    }
    private function lookahead()
    {
        return $this->tokens[$this->pos];
    }
    private function match($val)
    {
        $lookahead = $this->lookahead();
        if ($lookahead['value'] === $val) {
            $this->pos++;
        } else {
            $pos = $this->calculatePosition();
           throw new ParserException( $this->getErrorMessage($lookahead, $pos, __METHOD__,$this->getExpectedForMatch($val)));
        }
    }
    private function getExpectedForMatch($value){
        $tokens=[
            Token::PLUS,
            Token::MINUS,
            Token::MULTIPLY,
            Token::DIVIDE,
            Token::DOT,
            Token::TOBEEQUAL,
            Token::ELEMENTOF,
            Token::NOTELEMENTOF,
            Token::EQUAL,
            Token::SUBSETOF,
            Token::REALSUBSETOF,
            Token::COMPLEMENT,
            Token::UNION,
            Token::INTERSECTION,
            Token::SETMINUS,
            Token::COMMA,
            Token::DIVIDES,
            Token::DOESNOTDIVIDE,
            Token::VERTICALLINE,
            Token::LAND,
            Token::LOR,
            Token::LEFTCURLYBRACE,
            Token::RIGHTCURLYBRACE,
            Token::LEFTPARENTHESIS,
            Token::RIGHTPARENTHESIS,
            Token::LEFTSQUAREBRACKET,
            Token::RIGHTSQUAREBRACKET,
            Token::LESSTHAN,
            Token::GREATERTHAN,
            Token::LESSTHANOREQUAL,
            Token::GREATERTHANOREQUAL,
            Token::IDENTIFIER,
            Token::NUMBER,
            Token::EOL,        
            Token::VENN,
            Token::POINTSETDIAGRAM,
            Token::ADD,
            Token::DELETE,
        ];
        $expected=array_filter($tokens,function ($elem) use ($value) {
           return $elem['value']===$value;
        });
        $expected=reset($expected);
        return $expected;
    }
    private function getPossibleTokens($nonterminal){
        $possibletokens=[
            "statement"=>
            [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name'],
                TOKEN::LEFTCURLYBRACE['name'],
                TOKEN::LEFTSQUAREBRACKET['name'],
                TOKEN::IDENTIFIER['name'],
                Token::VERTICALLINE['name'],
                Token::VENN['name'],
                Token::POINTSETDIAGRAM['name']
            ], 
            "sexpr"=>
            [
                TOKEN::LEFTCURLYBRACE['name'],
                TOKEN::IDENTIFIER['name'],
                Token::LEFTSQUAREBRACKET['name']
            ], 
            "sexpr_"=>
            [
                Token::TOBEEQUAL['name'],
                Token::EQUAL['name'],
                Token::SUBSETOF['name'],
                Token::REALSUBSETOF['name'],
                Token::COMPLEMENT['name'],
                Token::UNION['name'],
                Token::INTERSECTION['name'],
                Token::SETMINUS['name'],
                Token::VERTICALLINE['name'],
                Token::EOL['name'],
                Token::IDENTIFIER['name']
            ], 
            "stesruisc"=>
            [
                Token::TOBEEQUAL['name'],
                Token::EQUAL['name'],
                Token::SUBSETOF['name'],
                Token::REALSUBSETOF['name'],
                Token::COMPLEMENT['name'],
                Token::UNION['name'],
                Token::INTERSECTION['name'],
                Token::SETMINUS['name']
            ], 
            "setoperationside"=>
            [
                Token::LEFTCURLYBRACE['name'],
                Token::IDENTIFIER['name']
            ], 
            "curliedsetexp"=>
            [
                Token::LEFTCURLYBRACE['name']
            ], 
            "setexp"=>
            [
                Token::MINUS['name'],
                Token::NUMBER['name'],
                Token::LEFTSQUAREBRACKET['name'],
                Token::IDENTIFIER['name']
            ], 
            "setformula"=>
            [
                Token::IDENTIFIER['name']
            ], 
            "logicalexp"=>
            [
                Token::MINUS['name'],
                Token::NUMBER['name'],
                Token::IDENTIFIER['name'],
                Token::LEFTPARENTHESIS['name']
            ], 
            "logicalexp_"=>
            [
                Token::LAND['name'],
                Token::LOR['name'],
                Token::RIGHTCURLYBRACE['name'],
                Token::RIGHTPARENTHESIS['name']
            ], 
            "logicaloperator"=>
            [
                Token::LAND['name'],
                Token::LOR['name']
            ], 
            "logicalsubexp"=>
            [
                Token::MINUS['name'],
                Token::NUMBER['name'],
                Token::IDENTIFIER['name'],
                Token::LEFTPARENTHESIS['name']
            ], 
            "comparsionoperator"=>
            [
                Token::EQUAL['name'],
                Token::LESSTHAN['name'],
                Token::GREATERTHAN['name'],
                Token::LESSTHANOREQUAL['name'],
                Token::GREATERTHANOREQUAL['name']
            ], 
            "divisibilityoperator"=>
            [
                Token::DIVIDES['name'],
                Token::DOESNOTDIVIDE['name']
            ], 
            "logicalrhs"=>
            [
                Token::MINUS['name'],
                Token::NUMBER['name'],
                Token::IDENTIFIER['name']
            ], 
            "logicalrhs_"=>
            [
                Token::PLUS['name'],
                Token::MINUS['name'],
                Token::MULTIPLY['name'],
                Token::DIVIDE['name']
            ], 
            "logicalrhs__"=>
            [
                Token::MINUS['name'],
                Token::NUMBER['name'],
                Token::IDENTIFIER['name']
            ], 
            "simpleoperator"=>
            [
                Token::PLUS['name'],
                Token::MINUS['name'],
                Token::MULTIPLY['name'],
                Token::DIVIDE['name']
            ], 
            "setliteral"=>
            [
                Token::MINUS['name'],
                Token::NUMBER['name']
            ], 
            "setliteral_"=>
            [
                Token::COMMA['name'],
                Token::RIGHTCURLYBRACE['name']
            ], 
            "pointsetliteral"=>
            [
                Token::LEFTSQUAREBRACKET['name'],
                Token::RIGHTCURLYBRACE['name']
            ], 
            "pointsetliteral_"=>
            [
                Token::COMMA['name'],
                Token::RIGHTCURLYBRACE['name']
            ], 
            "point"=>
            [
                Token::LEFTSQUAREBRACKET['name']
            ], 
            "selementofnelementof"=>
            [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name']
            ], 
            "selementofnelementof_"=>
            [
                TOKEN::ELEMENTOF['name'],
                TOKEN::NOTELEMENTOF['name']
            ], 
            "wholenumber"=> 
            [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name']
            ],
            "scardinality"=>
            [
                Token::VERTICALLINE['name']
            ], 
            "sofunctioncall"=>
            [
                Token::IDENTIFIER['name']
            ], 
            "sofunctionname"=>
            [
                Token::ADD['name'],
                Token::DELETE['name']
            ], 
            "sfunctioncall"=>
            [
                Token::VENN['name'],
                Token::POINTSETDIAGRAM['name']
            ], 
            "sfunctionname"=>
            [
                Token::POINTSETDIAGRAM['name'],
                Token::VENN['name']
            ], 
            "arguments"=>
            [
                Token::MINUS['name'],
                Token::NUMBER['name'],
                Token::IDENTIFIER['name'],
                Token::LEFTCURLYBRACE['name']
            ], 
            "arguments_"=>
            [
                Token::COMMA['name'],
                Token::RIGHTPARENTHESIS["name"]
            ], 
            "argument"=>
            [
                Token::MINUS['name'],
                Token::NUMBER['name'],
                Token::LEFTCURLYBRACE['name'],
                Token::IDENTIFIER['name']
            ], 
            "ssimpleexpression"=>
            [
                Token::VERTICALLINE['name']
            ], 
            "ssimpleexpression_"=>
            [
                Token::PLUS['name'],
                Token::MINUS['name'],
                Token::MULTIPLY['name'],
                Token::DIVIDE['name']
            ],
        ];
        return $possibletokens[$nonterminal];
    }
}