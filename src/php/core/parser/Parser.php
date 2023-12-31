<?php
namespace core\parser;



use \ReflectionFunction;

use \core\lib\datastructures\Map;
use \core\lib\Functions;
use \core\lib\PointSetDiagramFunctions;
use \core\lib\datastructures\Point as LPoint;
use \core\lib\datastructures\Set;
use \core\parser\exception\ParserException;
use \core\parser\exception\SemanticException;
use \core\parser\exception\UndefinedVariableException;

class Parser
{

    private $tokens;
    private $pos;
    private static ?Map $vars = null;
    private static Set $baseSet;

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
    /**
     * Parses a statement and returns an array of results.
     * A statement can be either an element of, a subset of, or a set expression,
     * or a simple expression, or a function call.
     * @return array|null An associative array of results, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
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
            case (Token::LEFTPARENTHESIS['name']):
            case (TOKEN::IDENTIFIER['name']):
                $result = $this->sexpr();
                break;
            case (Token::VERTICALLINE['name']):
                $result = $this->ssimpleexpression();
                break;
            case (Token::VENN['name']):
            case (Token::POINTSETDIAGRAM['name']):
                $result = $this->sfunctioncall();
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses an element of or not element of expression and returns a boolean value.
     * An element of or not element of expression consists of a whole number followed by an element of or not element of operator and a set.
     * @return bool|null A boolean value indicating whether the number is an element of or not an element of the set, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a whole number and returns its value.
     * A whole number can be either a positive or a negative integer.
     * @return int|null The value of the whole number, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
    private function wholenumber()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (TOKEN::MINUS['name']):
                $this->match('-');
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses an element of or not element of operator and a set and returns an array of results.
     * An element of or not element of operator is either &isin; or &notin;, and a set is either a set literal, a set identifier.
     * @return array|null An associative array of results, containing the operator and the set, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
    private function selementofnelementof_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (TOKEN::ELEMENTOF['name']):
                $this->match('&isin;');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->setoperationside();
                break;
            case (TOKEN::NOTELEMENTOF['name']):
                $this->match('&notin;');
                $result['op'] = $lookahead['value'];
                $result['set'] = $this->setoperationside();
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));
        }
        return $result;
    }

    /**
     * Parses a set operation side and returns its value.
     * A set operation side can be either a set literal enclosed by curly braces, or a set identifier that refers to a variable which is either exist or it does not.
     * @return mixed|null The value of the set operation side, which can be an array of elements, a string, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
    private function setoperationside()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::LEFTCURLYBRACE['name']):
                $result = $this->curliedsetexp();
                break;
            case (Token::IDENTIFIER['name']):
                $this->match($lookahead['value']);
                $result = Parser::$vars->get($lookahead['value']);
                if ($result == null) {
                    $result = $lookahead['value'];
                }
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a set literal enclosed by curly braces and returns its value.
     * A set literal consists of a left curly brace, a set expression, and a right curly brace.
     * @return array|null The value of the set literal, which is an array of elements, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a set expression and returns its value.
     * A set expression can be either a set literal consisting of a comma-separated list of whole numbers,
     * or a point set literal consisting of a comma-separated list of points enclosed by square brackets,
     * or a set formula consisting of a start and end value, an optional filter formula, and a formula to generate the elements.
     * @return Set|null The value of the set expression, which is an object of the Set class, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
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
                $result = PointSetDiagramFunctions::createSetFromPointArray($pointsetliteral);
                break;
            case (Token::IDENTIFIER['name']):
                $this->match($lookahead['value']);
                $id['id'] = $lookahead['value'];
                $rest = $this->setexp_();
                if (isset($rest['verticalline'])) {

                    $setformula = array_merge_recursive($id, $rest['logicalexp']);
                    $setformula = Functions::flatSetFormula($setformula);
                    $setformula = Functions::extractArrayFromArray($setformula);
                    $setformula = Functions::reAppendVarnameToArrayKeys($setformula);
                    if (!Functions::isVariablesGood(array_keys($setformula)))
                        throw new SemanticException("At least one of the identifiers is misspelled in the formula.");
                    $bounds = Functions::collectBounds($setformula);
                    $bounds = Functions::getMinMax($bounds);
                    $boundfuncswithop = Functions::collectBoundFuncs($setformula);
                    $boundfuncswithop = Functions::removeDuplicatedOperator($boundfuncswithop);
                    $boundformula = Functions::concatBoundConditions($boundfuncswithop);
                    $formula = Functions::getFuncDef($setformula);
                    $setformula = ['start' => $bounds['start'], 'end' => $bounds['end'], 'filterformula' => $boundformula, 'formula' => $formula];
                    $result = Functions::createSetFromFormula($setformula['start'], $setformula['end'], $setformula['filterformula'], $setformula['formula']);
                } else {
                    $idarr = [$id['id'], ...$rest];
                    $vars = $this->getVarsByIds($idarr);
                    $wrog = array_filter($vars, function ($var) {
                        return $var === null;
                    });
                    if (!empty($wrog))
                        throw new UndefinedVariableException('The following variables are not defined: ' . json_encode($wrog));
                    $result = PointSetDiagramFunctions::createSetFromPointArray($vars);
                }
                break;
            case (Token::RIGHTCURLYBRACE['name']):
                $result = new Set([]);
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }

        return $result;
    }

    /**
     * Parses a set expression and returns an array of identifiers or a set formula.
     *
     * @return array|null The parsed set expression or null if an error occurs.
     * @throws ParserException If an unexpected token is encountered.
     */
    private function setexp_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::COMMA['name']):
                $result = $this->identifierliteral();
                break;
            case (Token::VERTICALLINE['name']):
                $result = $this->setformula();
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }

        return $result;
    }

    /**
     * Parses an identifier literal and returns an array of identifiers.
     *
     * @return array The parsed identifier literal or an empty array if none is found.
     * @throws ParserException If an unexpected token is encountered.
     */
    private function identifierliteral()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::COMMA['name']):
                $this->match(',');
                $lookahead = $this->lookahead();
                ;
                $id = $lookahead['value'];
                $this->match($lookahead['value']);
                $rest = $this->identifierliteral();
                $result = Functions::removeNullFromArray([$id, ...$rest]);
                break;
            case (Token::RIGHTCURLYBRACE['name']):
                $result = [];
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }

        return $result;
    }

    /**
     * Parses a set formula and returns an array of results.
     * A set formula consists of an identifier, a vertical line, and a logical expression that defines the elements of the set.
     * The logical expression can contain comparison operators, arithmetic operators, user-defined functions, and bound functions.
     * @return array|null An associative array of results, containing the start and end value of the set, the filter formula that checks the element conditions, and the formula that generates the elements, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     * @throws SemanticException If the identifier is misspelled or the bound functions are inconsistent in the logical expression.
     */
    private function setformula()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::VERTICALLINE['name']):
                $this->match('|');
                $logicalexp = $this->logicalexp();
                $result = ['verticalline' => '|', 'logicalexp' => $logicalexp];
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a logical expression and returns a tree of results.
     * A logical expression consists of one or more logical subexpressions connected by logical operators.
     * A logical subexpression can be either a comparison operator followed by a logical right-hand side,
     * or an arrow operator followed by a user-defined function name, a simple operator and a whole number.
     * A logical operator can be either AND, OR, or XOR.
     * @return array|null A tree of results, where each node is either a logical operator or a logical subexpression, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
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
                $result = Functions::transformConditionsToTree($merged);
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a logical subexpression and returns an array of results.
     * A logical subexpression can be either a whole number followed by a divisibility operator and an identifier,
     * or an identifier followed by a logical subexpression_,
     * or a logical expression enclosed by parentheses.
     * A divisibility operator can be either DIVIDES or NOTDIVIDES.
     * @return array|null An associative array of results, where each key is an identifier and each value is an array of divisibility conditions, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
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
                $rest['dividesfunc'] = Functions::createDivisibilityCondition($num, $divop);
                $rest['dividesnum'] = $num;
                $result[$identifier][] = Functions::appendVarnameToArrayKeys($rest, $identifier);
                break;
            case (Token::IDENTIFIER['name']):
                $this->match($lookahead['value']);
                $identifier = $lookahead['value'];
                $rest = $this->logicalsubexp_();
                $result[$identifier][] = Functions::appendVarnameToArrayKeys($rest, $identifier);
                break;
            case (Token::LEFTPARENTHESIS['name']):
                $this->match('(');
                $logicalexp = $this->logicalexp();
                $this->match(')');
                $result = ['(', $logicalexp, ')'];
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a logical subexpression and returns an array of results.
     * A logical subexpression can be either a comparison operator followed by a logical right-hand side,
     * or an arrow operator followed by a user-defined function name, a simple operator and a whole number.
     * @return array|null An associative array of results, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
    private function logicalsubexp_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::EQUAL['name']):
            case (Token::LESSTHAN['name']):
            case (Token::GREATERTHAN['name']):
            case (Token::LESSTHANOREQUAL['name']):
            case (Token::GREATERTHANOREQUAL['name']):
                $comparsionop = $this->comparsionoperator();
                $logicalrhs = $this->logicalrhs();
                $result['boundfunc'] = Functions::createComparsionCondition($comparsionop, Functions::processLogicalRhs($logicalrhs));
                $result['boundvalue'] = $logicalrhs['num'];
                break;
            case (Token::ARROW['name']):
                $result = $this->functiondefinition();

                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a function definition and returns an array of user-defined functions.
     * @return array|null The parsed function definition or null if an error occurs.
     * @throws ParserException If an unexpected token is encountered.
     */
    private function functiondefinition()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::ARROW['name']):
                $this->match('->');
                $result = $this->functiondefinition_();
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));
        }
        return $result;
    }

    /**
     * Parses a function definition after the arrow and returns an array of user-defined functions.
     * @return array The parsed function definition or an empty array if none is found.
     * @throws ParserException If an unexpected token is encountered.
     */
    private function functiondefinition_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
                $num = $this->wholenumber();
                $simpleop = $this->simpleoperator();
                $lookahead = $this->lookahead();
                $this->match($lookahead['value']);
                $result[$lookahead['value'] . '_funcdef'] = Functions::createUserFunction($simpleop, $num);
                break;

            case (Token::IDENTIFIER['name']):
                $lookahead = $this->lookahead();
                $this->match($lookahead['value']);
                $simpleop = $this->simpleoperator();
                $num = $this->wholenumber();
                $result[$lookahead['value'] . '_funcdef'] = Functions::createUserFunction($simpleop, $num);
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));
        }
        return $result;
    }

    /**
     * Parses a divisibility operator and returns its value.
     * A divisibility operator can be either &mid; (divides) or &nmid; (does not divide).
     * @return string|null The value of the divisibility operator, which is either "&mid;" or "&nmid;", or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
    private function divisibilityoperator()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::DIVIDES['name']):
                $this->match('&mid;');
                $result = $lookahead['value'];
                break;
            case (Token::DOESNOTDIVIDE['name']):
                $this->match('&nmid;');
                $result = $lookahead['value'];
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }

        return $result;
    }

    /**
     * Parses a comparison operator and returns its value.
     * A comparison operator can be either = (equal), < (less than), > (greater than), <= (less than or equal), or >= (greater than or equal).
     * @return string|null The value of the comparison operator, which is one of the symbols mentioned above, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a logical right-hand side and returns an array of results.
     * A logical right-hand side can be either a whole number followed by a logical right-hand side_, or an identifier followed by a logical right-hand side_.
     * A logical right-hand side_ can contain divisibility operators, arithmetic operators, user-defined functions, and bound functions.
     * @return array|null An associative array of results, where each key is either 'num' or an identifier and each value is an array of logical conditions, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }

        return $result;
    }

    /**
     * Parses a logical right-hand side_ and returns an array of results.
     * A logical right-hand side_ can be either a simple operator followed by a logical right-hand side__, or an empty string.
     * A simple operator can be either + (plus), - (minus), * (multiply), or / (divide).
     * @return array|null An associative array of results, containing the simple operator and the logical right-hand side__, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a logical right-hand side__ and returns an array of results.
     * A logical right-hand side__ can be either a whole number or an identifier.
     * @return array|null An associative array of results, containing either 'num' or 'id' as the key and the value of the whole number or the identifier as the value, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
    private function logicalrhs__()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
                $result['num'] = $this->wholenumber();
                break;
            case (Token::IDENTIFIER['name']):
                $this->match($lookahead['value']);
                $result['id'] = $lookahead['value'];
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a logical expression_ and returns an array of results.
     * A logical expression_ can be either a logical operator followed by a logical expression, or an empty string.
     * A logical operator can be either AND, OR.
     * @return array|null An associative array of results, containing the logical operator and the logical expression, or null if the lookahead token is not valid.
     * @throws ParserException If the lookahead token does not match the expected token.
     */
    private function logicalexp_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::LAND['name']):
            case (Token::LOR['name']):
                $logicalop = $this->logicaloperator();
                $rest = $this->logicalexp();
                $result = array_merge_recursive(['logicalop' => $logicalop], ['subexp' => [...$rest]]);
                break;
            case (Token::RIGHTCURLYBRACE['name']):
            case (Token::RIGHTPARENTHESIS['name']):
                $result = [];
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a logical operator token and returns its value.
     * A logical operator can be either &and; (AND) or &or; (OR).
     * @return string|null The value of the logical operator token, or null if no match is found.
     * @throws ParserException If the lookahead token is not a valid logical operator.
     */
    private function logicaloperator()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::LAND['name']):
                $this->match('&and;');
                $result = $lookahead['value'];
                break;
            case (Token::LOR['name']):
                $this->match('&or;');
                $result = $lookahead['value'];
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a set literal token and returns its value as an array of numbers.
     * @return array|null The value of the set literal token, or null if no match is found.
     * @throws ParserException If the lookahead token is not a valid start of a set literal.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a comma or a right curly brace token and returns the rest of the set literal value as an array of numbers.
     * @return array|null The rest of the set literal value, or null if no match is found.
     * @throws ParserException If the lookahead token is not a valid end of a set literal.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a point set literal token and returns its value as an array of points.
     * @return array|null The value of the point set literal token, or null if no match is found.
     * @throws ParserException If the lookahead token is not a valid start of a point set literal.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a comma or a right curly brace token and returns the rest of the point set literal value as an array of points.
     * @return array|null The rest of the point set literal value, or null if no match is found.
     * @throws ParserException If the lookahead token is not a valid end of a point set literal.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a set expression token and returns its value as a set, a point, a boolean, or a string.
     * @return mixed The value of the set expression token, depending on the type of operation performed.
     * @throws ParserException If the lookahead token is not a valid start of a set expression, or if the operation is invalid or undefined.
     */
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
                        case (Token::TOBEEQUAL['value']):
                            if (gettype($setoperationside) === 'string') {
                                $rest = Functions::flatSetExpression($rest);
                                unset($rest[0]);
                                $rest = array_values($rest);
                                Parser::setBaseSet(Functions::createBaseSet($this->getVars()));
                                $rest['set'] = Functions::evaluateSetExpression($rest);
                                if (isset($rest['set'])) {
                                    $result = $rest['set'];
                                    Parser::$vars->add($setoperationside, $result);
                                    Parser::setBaseSet(Functions::createBaseSet($this->getVars()));
                                } else if (isset($rest['point'])) {
                                    Parser::$vars->add($setoperationside, $rest['point']);
                                    $result = $rest['point'];
                                }
                            }
                            break;
                        case (Token::COMPLEMENT['value']):
                            Parser::setBaseSet(Functions::createBaseSet($this->getVars()));
                            $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => $setoperationside], ['rhs' => $rest]));
                            $result = Functions::evaluateSetExpression($array);
                            break;
                        case (Token::DOT['value']):
                            if (gettype($setoperationside) === 'string') {
                                if (Parser::$vars->has($setoperationside)) {
                                    $set = Parser::$vars->get($setoperationside);
                                    $funcname = $rest['funcname'] . 'Element';
                                    $result = Functions::$funcname($rest['arguments'][0], $set);
                                } else {
                                    throw new ParserException("Variable called $setoperationside does not exist.");
                                }
                            } else {
                                $funcname = $rest['funcname'] . 'Element';
                                $result = Functions::$funcname($rest['arguments'][0], $setoperationside);
                            }

                            break;
                        case (Token::EQUAL['value']):
                            $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => $setoperationside], ['rhs' => $rest]));
                            $result = Functions::evaluateSetExpression($array);
                            break;
                        case (Token::SUBSETOF['value']):
                            $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => $setoperationside], ['rhs' => $rest]));
                            $result = Functions::evaluateSetExpression($array);
                            break;
                        case (Token::REALSUBSETOF['value']):
                            $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => $setoperationside], ['rhs' => $rest]));
                            $result = Functions::evaluateSetExpression($array);
                            break;
                        case (Token::UNION['value']):
                            $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => $setoperationside], ['rhs' => $rest]));
                            $result = Functions::evaluateSetExpression($array);
                            break;
                        case (Token::INTERSECTION['value']):
                            $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => $setoperationside], ['rhs' => $rest]));
                            $result = Functions::evaluateSetExpression($array);
                            break;
                        case (Token::SETMINUS['value']):
                            $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => $setoperationside], ['rhs' => $rest]));
                            $result = Functions::evaluateSetExpression($array);
                            break;
                        default:
                            $pos = $this->calculatePosition();
                            throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

                    }

                } else if (empty($rest)) {
                    $result = $setoperationside;
                }
                break;
            case (Token::LEFTPARENTHESIS['name']):
                $this->match('(');
                $setoperationside = $this->setoperationside();
                $rest = $this->stesruisc__();
                $this->match(')');
                $rest2 = $this->stesruisc__();
                $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => ['lparen' => '(', 'lhs' => $setoperationside, 'rhs' => $rest, 'rparen' => ')'], ['rhs' => $rest2]]));
                $result = Functions::evaluateSetExpression($array);
                if (Functions::isArray($result) && count($result) == 2 && in_array(Token::COMPLEMENT['value'], $result)) {
                    $baseSet = Functions::createBaseSet($this->getVars());
                    $result = Functions::complement($result[0], $baseSet);
                }
                break;
            case (Token::LEFTSQUAREBRACKET['name']):
                $result = $this->point();
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a set expression continuation token and returns its value as a set, a point, a boolean, a string, or false.
     * @return mixed The value of the set expression continuation token, depending on the type of operation performed, or false if no match is found.
     * @throws ParserException If the lookahead token is not a valid continuation of a set expression.
     */
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
            case (Token::VERTICALLINE['name']):
            case (Token::EOL['name']):
                $result = $this->stesruisc();

                break;
            case (Token::DOT['name']):
                $result = $this->sofunctioncall();
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a set expression continuation token and returns its value as a set, a point, a boolean, a string, or an array.
     * @return mixed The value of the set expression continuation token, depending on the type of operation performed.
     * @throws ParserException If the lookahead token is not a valid continuation of a set expression, or if the operation is invalid or undefined.
     */
    private function stesruisc()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {

            case (Token::TOBEEQUAL['name']):
                $this->match(':=');
                $op = $lookahead['value'];
                $rest = $this->trhs();
                $result = array_merge_recursive(['op' => $op], ['rhs' => $rest]);
                break;

            case (Token::EQUAL['name']):
                $this->match('=');
                $op = $lookahead['value'];
                $rest = $this->stesruisc_();
                $result = array_merge_recursive(['op' => $op], ['rhs' => $rest]);
                break;

            case (Token::SUBSETOF['name']):
                $this->match('&sube;');
                $op = $lookahead['value'];
                $rest = $this->stesruisc_();
                $result = array_merge_recursive(['op' => $op], ['rhs' => $rest]);
                break;

            case (Token::REALSUBSETOF['name']):
                $this->match('&sub;');
                $op = $lookahead['value'];
                $rest = $this->stesruisc_();
                $result = array_merge_recursive(['op' => $op], ['rhs' => $rest]);
                break;

            case (Token::COMPLEMENT['name']):
            case (Token::UNION['name']):
            case (Token::INTERSECTION['name']):
            case (Token::SETMINUS['name']):
            case (TOKEN::VERTICALLINE['name']):
            case (Token::EOL['name']):
                $result = $this->uisc();
                break;

            case (Token::LEFTPARENTHESIS['name']):
                $this->match('(');
                $setoperationside = $this->setoperationside();
                $rest = $this->stesruisc__();
                $this->match(')');
                $rest2 = $this->stesruisc__();
                $array = Functions::flatSetExpression(array_merge_recursive(['lparen' => '(',], ['lhs' => $setoperationside], ['rest' => $rest], ['rparen' => ')'], ['rest2' => $rest2]));
                $result = Functions::evaluateSetExpression($array);
                break;

            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a right-hand side token of a set assignment and returns its value as a set, a point, or an array.
     * @return mixed The value of the right-hand side token, depending on the type of expression parsed.
     * @throws ParserException If the lookahead token is not a valid start of a right-hand side expression.
     */
    private function trhs()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {

            case (Token::LEFTCURLYBRACE['name']):
            case (Token::IDENTIFIER['name']):
                $setoperationside = $this->setoperationside();
                $rest = $this->stesruisc__();
                $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => $setoperationside], ['rest' => $rest]));
                $result['set'] = Functions::evaluateSetExpression($array);
                break;
            case (Token::LEFTPARENTHESIS['name']):
                $this->match('(');
                $setoperationside = $this->setoperationside();
                $rest = $this->stesruisc__();
                $this->match(')');
                $rest2 = $this->stesruisc__();
                $array = Functions::flatSetExpression(array_merge_recursive(['lparen' => '(',], ['lhs' => $setoperationside], ['rest' => $rest], ['rparen' => ')'], ['rest2' => $rest2]));
                $result['set'] = Functions::evaluateSetExpression($array);
                break;

            case (Token::LEFTSQUAREBRACKET['name']):
                $result['point'] = $this->point();
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a set expression continuation token and returns its value as a set, an array, or an empty array.
     * @return array The value of the set expression continuation token, depending on the type of expression parsed.
     * @throws ParserException If the lookahead token is not a valid continuation of a set expression, or if the operation is invalid or undefined.
     */
    private function stesruisc_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {

            case (Token::LEFTCURLYBRACE['name']):
            case (Token::IDENTIFIER['name']):
                $setoperationside = $this->setoperationside();
                $rest = $this->stesruisc__();
                $result = array_merge_recursive(['lhs' => $setoperationside], ['rhs' => $rest]);
                break;
            case (Token::LEFTPARENTHESIS['name']):
                $this->match('(');
                $setoperationside = $this->setoperationside();
                $rest = $this->stesruisc__();
                $this->match(')');
                $rest2 = $this->stesruisc__();
                $result = array_merge_recursive(['lparen' => '(',], ['lhs' => $setoperationside], ['rest' => $rest], ['rparen' => ')'], ['rest2' => $rest2]);
                break;
            case (Token::RIGHTPARENTHESIS['name']):
            case (Token::VERTICALLINE['name']):
            case (Token::EOL['name']):
                $result = [];
                break;

            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a set expression continuation token and returns its value as a set or an empty set.
     * @return array The value of the set expression continuation token, depending on the type of operation performed, or an empty set if no match is found.
     * @throws ParserException If the lookahead token is not a valid continuation of a set expression, or if the operation is invalid or undefined.
     */
    private function stesruisc__()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {

            case (Token::UNION['name']):
            case (Token::INTERSECTION['name']):
            case (Token::SETMINUS['name']):
            case (Token::COMPLEMENT['name']):
            case (Token::RIGHTPARENTHESIS['name']):
            case (Token::VERTICALLINE['name']):
            case (Token::EOL['name']):
                $result = $this->uisc();
                break;

            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a union, intersection, set minus, or complement token and returns its value as an array.
     * @return array|null The value of the union, intersection, set minus, or complement token, or null if no match is found.
     */
    private function uisc()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {

            case (Token::UNION['name']):
                $this->match('&cup;');
                $op = $lookahead['value'];
                $rest = $this->stesruisc_();
                $result = array_merge_recursive(['op' => $op], ['rhs' => $rest]);
                break;
            case (Token::INTERSECTION['name']):
                $this->match('&cap;');
                $op = $lookahead['value'];
                $rest = $this->stesruisc_();
                $result = array_merge_recursive(['op' => $op], ['rhs' => $rest]);
                break;
            case (Token::SETMINUS['name']):
                $this->match('&setminus;');
                $op = $lookahead['value'];
                $rest = $this->stesruisc_();
                $result = array_merge_recursive(['op' => $op], ['rhs' => $rest]);
                break;
            case (Token::COMPLEMENT['name']):
                $this->match('&comp;');
                $op = $lookahead['value'];
                $rest = $this->uisc();
                $result = array_merge_recursive(['op' => $op], ['rhs' => $rest]);
                break;
            case (Token::RIGHTPARENTHESIS['name']):
            case (Token::VERTICALLINE['name']):
            case (Token::EOL['name']):
                $result = [];
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));
        }

        return $result;
    }

    /**
     * Parses a set operation function call token and returns its value as an array.
     * @return array The value of the set operation function call token, containing the operator, the function name, and the arguments.
     * @throws ParserException If the lookahead token is not a valid start of a set operation function call.
     */
    private function sofunctioncall()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::DOT['name']):
                $this->match('.');
                $funcname = $this->sofunctionname();
                $this->match('(');
                $arguments = $this->arguments();
                $this->match(')');
                $result = ['op' => '.', 'funcname' => $funcname, 'arguments' => $arguments];
                break;

            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));
        }
        return $result;

    }

    /**
     * Parses a set operation function name token and returns its value as a string.
     * @return string The value of the set operation function name token, either 'add' or 'delete'.
     * @throws ParserException If the lookahead token is not a valid set operation function name.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses an argument list token and returns its value as an array of numbers, strings, or sets.
     * @return array The value of the argument list token, containing the arguments separated by commas.
     * @throws ParserException If the lookahead token is not a valid start of an argument list, or if the argument is invalid or undefined.
     */
    private function arguments()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::MINUS['name']):
            case (Token::NUMBER['name']):
                $arg = $this->argument();
                $rest = $this->arguments_();
                $result = Functions::isObject($rest) ? [$arg, $rest] : [$arg, ...$rest];
                break;
            case (Token::IDENTIFIER['name']):
                $arg = $this->argument();
                if ($arg === null) {
                    $pos = $this->calculatePosition();
                    throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));
                }
                $rest = $this->arguments_();
                $result = Functions::isObject($rest) ? [$arg, $rest] : [$arg, ...$rest];
                break;
            case (Token::LEFTCURLYBRACE['name']):
                $arg = $this->argument();
                $rest = $this->arguments_();
                $result = Functions::removeEmptyArrayFromArray([...$arg, $rest]);
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses an argument token and returns its value as a number, a string, or a set.
     * @return mixed The value of the argument token, depending on the type of expression parsed.
     * @throws ParserException If the lookahead token is not a valid start of an argument, or if the argument is invalid or undefined.
     */
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
                $result = Parser::$vars->get($lookahead['value']);
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));
        }
        return $result;
    }

    /**
     * Parses an argument list continuation token and returns its value as a number, a string, a set, or an empty array.
     * @return mixed The value of the argument list continuation token, depending on the type of expression parsed, or an empty array if no match is found.
     * @throws ParserException If the lookahead token is not a valid continuation of an argument list.
     */
    private function arguments_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::COMMA['name']):
                $this->match(',');
                $result = $this->argument();
                break;
            case (Token::RIGHTPARENTHESIS['name']):
                $result = [];
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a point token and returns its value as an LPoint object.
     * @return LPoint The value of the point token, containing the x and y coordinates of the point.
     * @throws ParserException If the lookahead token is not a valid start of a point token, or if the syntax is incorrect.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a simple expression token and returns its value as a number.
     * @return number The value of the simple expression token, depending on the type of arithmetic operation performed.
     * @throws ParserException If the lookahead token is not a valid start of a simple expression, or if the operation is invalid or undefined.
     */
    private function ssimpleexpression()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::VERTICALLINE['name']):
                $cardinality = $this->scardinality();
                $rest = $this->ssimpleexpression_();
                $array = [$cardinality, ...$rest];
                $result = Functions::evaluateSimpleExpression($array);
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;

    }

    /**
     * Parses a cardinality token and returns its value as a number.
     * @return mixed The value of the cardinality token, which is the number of elements in the set expression.
     * @throws ParserException If the lookahead token is not a valid start of a cardinality token, or if the set expression is invalid or undefined.
     */
    private function scardinality()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::VERTICALLINE['name']):
                $this->match('|');
                $result = $this->scardinality_();
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Calculates the cardinality of a set expression.
     *
     * This function parses a set expression and evaluates it using the Functions class.
     * It then returns the cardinality of the resulting set using the cardinality function.
     * The set expression can be either a simple set, an identifier, or a parenthesized expression.
     * The function expects a vertical bar `|` at the end of the expression.
     * @return int|null The cardinality of the set expression, or null if there is a syntax error.
     * @throws ParserException If there is a lexical error or an undefined identifier.
     */
    private function scardinality_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::LEFTCURLYBRACE['name']):
            case (Token::IDENTIFIER['name']):
                $setoperationside = $this->setoperationside();
                $rest = $this->sexpr_();
                $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => $setoperationside], ['rhs' => $rest]));
                $set = Functions::evaluateSetExpression($array);
                $this->match('|');
                Parser::setBaseSet(Functions::createBaseSet($this->getVars()));
                $result = Functions::cardinality($set);
                break;
            case (Token::LEFTPARENTHESIS['name']):
                $this->match('(');
                $setoperationside = $this->setoperationside();
                $rest = $this->sexpr_();
                $this->match(')');
                $rest2 = $this->uisc();
                $this->match('|');
                Parser::setBaseSet(Functions::createBaseSet($this->getVars()));
                $array = Functions::flatSetExpression(array_merge_recursive(['lhs' => ['lparen' => '(', 'lhs' => $setoperationside, 'rhs' => $rest, 'rparen' => ')'], ['rhs' => $rest2]]));
                $set = Functions::evaluateSetExpression($array);
                $result = Functions::cardinality($set);
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));
        }
        return $result;
    }

    /**
     * Parses a simple expression continuation token and returns its value as a number or false.
     * @return mixed The value of the simple expression continuation token, depending on the type of arithmetic operation performed, or false if no match is found.
     * @throws ParserException If the lookahead token is not a valid continuation of a simple expression, or if the operation is invalid or undefined.
     */
    private function ssimpleexpression_()
    {
        $result = null;
        $lookahead = $this->lookahead();
        switch ($lookahead['type']) {
            case (Token::PLUS['name']):
            case (Token::MINUS['name']):
            case (Token::MULTIPLY['name']):
            case (Token::DIVIDE['name']):
                $result[] = $this->simpleoperator();
                $result[] = $this->scardinality();
                $rest = $this->ssimpleexpression_();
                $result = [...$result, ...$rest];
                break;
            case (Token::EOL['name']):
                $result = [];
                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a simple operator token and returns its value as a string.
     * @return string The value of the simple operator token, either '+', '-', '*', or '/'.
     * @throws ParserException If the lookahead token is not a valid simple operator.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }

        return $result;
    }

    /**
     * Parses a set function call token and returns its value as a string or an image.
     * @return mixed The value of the set function call token, either a string representing a Venn diagram, or an image representing a point set diagram.
     * @throws ParserException If the lookahead token is not a valid start of a set function call, or if the arguments are invalid or undefined.
     */
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
                if ($name === 'Venn') {
                    $result = Functions::$name(...$arguments);
                } else if ($name === 'PointSetDiagram') {
                    if (PointSetDiagramFunctions::isPointSetArray($arguments)) {
                        $argumentSet = Functions::union(...$arguments);
                    } else {
                        $argumentSet = PointSetDiagramFunctions::createSetFromPointArray($arguments);
                    }

                    $result = PointSetDiagramFunctions::$name($argumentSet);
                }

                break;
            default:
                $pos = $this->calculatePosition();
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Parses a set function name token and returns its value as a string.
     * @return string The value of the set function name token, either 'PointSetDiagram' or 'Venn'.
     * @throws ParserException If the lookahead token is not a valid set function name.
     */
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
                throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getPossibleTokens(__FUNCTION__)));

        }
        return $result;
    }

    /**
     * Constructs a new Parser object with a given array of tokens.
     * @param array $tokens An array of tokens to be parsed.
     */
    public function __construct($tokens = [])
    {
        $this->setTokens($tokens);
        $this->setVars();
    }

    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
        $this->pos = 0;


    }

    /**
     * Returns the Map object that contains the variables and their values.
     * @return Map The Map object that stores the variables and their values.
     */
    public function getVars()
    {
        return Parser::$vars;
    }

    public function setVars($vars = new Map([]))
    {
        Parser::$vars = $vars;
    }

    private function getVarsByIds($ids)
    {
        $vars = [];
        foreach ($ids as $value) {
            $vars[$value] = Parser::$vars->get($value);
        }
        return $vars;
    }
    public static function getBaseSet()
    {
        return Parser::$baseSet;
    }
    public static function setBaseSet($set)
    {
        Parser::$baseSet = $set;
    }

    /**
     * Returns the return type of a given function name as a string.
     * @param string $funcname The name of the function to get the return type of.
     * @return string The return type of the function as a string.
     */
    private function getFunctionReturnType($funcname): string
    {
        $reflectionFunction = new ReflectionFunction($funcname);
        return (string) $reflectionFunction->getReturnType();
    }

    /**
     * Returns the returned value of a function call as an object of the same type as the return type of the function.
     * @param mixed $result The returned value of the function call.
     * @param string $funcAsString The name of the function as a string.
     * @return object The returned value as an object of the same type as the return type of the function.
     */
    private function getReturnedValueAsReturTypeObject($result, $funcAsString)
    {
        return array_reduce(array_keys($result), function ($carry, $key) use ($result) {
            $carry->$key = $result[$key];
            return $carry;
        }, new $funcAsString());
    }

    /**
     * Returns the string representation of a given value.
     * @param mixed $result The value to be converted to a string.
     * @return string The string representation of the value, or 'null' if the value is null, or 'true' or 'false' if the value is a boolean.
     */
    private function getStringRepresentation($result)
    {
        if (is_null($result)) {
            return 'null';
        } else if (is_bool($result)) {
            return $result ? 'true' : 'false';
        } else if (is_array($result)) {
            return json_encode($result);
        } else {
            return (string) $result;
        }
    }

    /**
     * Returns an error message for a given lookahead token, position, function name, and expected tokens.
     * @param array $lookahead The lookahead token that caused the error.
     * @param int $pos The position of the error in the token array.
     * @param string $func The name of the function where the error occurred.
     * @param array $expected The array of expected tokens that would have prevented the error.
     * @return string The error message that describes the error and the expected tokens.
     */
    private function getErrorMessage($lookahead, $pos, $func, $expected)
    {
        return "Unexpected token $lookahead[type] ($lookahead[value]) at position " . ($pos + 1) . " in func $func; excepted token is one of the following: " . json_encode($expected);
    }

    /**
     * Calculates the position of the current token in the input string.
     * @return int The position of the current token in the input string.
     */
    private function calculatePosition()
    {
        $pos = 0;
        for ($i = 0; $i < $this->pos; $i++) {
            $pos += strlen(strval($this->tokens[$i]['value']));
        }
        return $pos;
    }

    /**
     * Returns the current token without consuming it.
     * @return array The current token in the token array.
     */
    private function lookahead()
    {
        return $this->tokens[$this->pos];
    }

    /**
     * Consumes the current token if it matches the given value, or throws an exception otherwise.
     * @param string $val The value to be matched with the current token.
     * @throws ParserException If the current token does not match the given value.
     */
    private function match($val)
    {
        $lookahead = $this->lookahead();
        if ($lookahead['value'] === $val) {
            $this->pos++;
        } else {
            $pos = $this->calculatePosition();
            throw new ParserException($this->getErrorMessage($lookahead, $pos, __METHOD__, $this->getExpectedForMatch($val)));
        }
    }

    /**
     * Returns the expected token for a given value to be matched with the current token.
     * @param string $value The value to be matched with the current token.
     * @return array|null The expected token that has the same value as the given value, or null if no such token exists.
     */
    private function getExpectedForMatch($value)
    {
        $tokens = [
            Token::PLUS,
            Token::MINUS,
            Token::MULTIPLY,
            Token::DIVIDE,
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
            Token::VENN,
            Token::POINTSETDIAGRAM,
            Token::ADD,
            Token::DELETE,
            Token::IDENTIFIER,
            Token::NUMBER,
            Token::VERTICALLINE,
            Token::EOL,
            Token::DOT,

        ];
        $expected = array_filter($tokens, function ($elem) use ($value) {

            return ($elem['value'] === $value) || (str_starts_with($elem['value'], '^') && str_ends_with($elem['value'], '$') && boolval(preg_match("/" . $elem['value'] . "/", $value)));
        });
        $reserved = array('add', 'delete', 'pointsetdiagram', 'venn');
        if (!empty($expected) && in_array($expected[array_key_first($expected)]['name'], $reserved)) {
            $expected = reset($expected);
        } else if (in_array(Token::IDENTIFIER, $expected)) {
            $expected = [];
            $expected['name'] = 'identifier';
            $expected['value'] = $value;
        } else if (in_array(Token::NUMBER, $expected)) {
            $expected = [];
            $expected['name'] = 'number';
            $expected['value'] = $value;
        } else {
            $expected = reset($expected);
        }
        return $expected ?: null;
    }

    /**
     * Returns an array of possible tokens for a given nonterminal symbol in the grammar.
     * @param string $nonterminal The name of the nonterminal symbol to get the possible tokens for.
     * @return array The array of possible tokens that can start or continue the nonterminal symbol, or an empty array if no such symbol exists.
     */
    private function getPossibleTokens($nonterminal)
    {
        $possibletokens = [
            "statement" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name'],
                TOKEN::LEFTCURLYBRACE['name'],
                TOKEN::LEFTSQUAREBRACKET['name'],
                TOKEN::LEFTPARENTHESIS['name'],
                TOKEN::IDENTIFIER['name'],
                TOKEN::VERTICALLINE['name'],
                TOKEN::VENN['name'],
                TOKEN::POINTSETDIAGRAM['name']
            ],
            "selementofnelementof" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name']
            ],
            "wholenumber" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name']
            ],
            "selementofnelementof_" => [
                TOKEN::ELEMENTOF['name'],
                TOKEN::NOTELEMENTOF['name']
            ],
            "setoperationside" => [
                TOKEN::LEFTCURLYBRACE['name'],
                TOKEN::IDENTIFIER['name']
            ],
            "curliedsetexp" => [
                TOKEN::LEFTCURLYBRACE['name']
            ],
            "setexp" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name'],
                TOKEN::LEFTSQUAREBRACKET['name'],
                TOKEN::IDENTIFIER['name'],
                TOKEN::RIGHTCURLYBRACE['name']
            ],
            "setexp_" => [
                TOKEN::COMMA['name'],
                TOKEN::VERTICALLINE['name']
            ],
            "identifierliteral" => [
                TOKEN::COMMA['name'],
                TOKEN::RIGHTCURLYBRACE['name']
            ],
            "setformula" => [
                TOKEN::VERTICALLINE['name']
            ],
            "logicalexp" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name'],
                TOKEN::IDENTIFIER['name'],
                TOKEN::LEFTPARENTHESIS['name']
            ],
            "logicalsubexp" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name'],
                TOKEN::IDENTIFIER['name'],
                TOKEN::LEFTPARENTHESIS['name']
            ],
            "logicalsubexp_" => [
                TOKEN::EQUAL['name'],
                TOKEN::LESSTHAN['name'],
                TOKEN::GREATERTHAN['name'],
                TOKEN::LESSTHANOREQUAL['name'],
                TOKEN::GREATERTHANOREQUAL['name'],
                TOKEN::ARROW['name']
            ],
            "functiondefinition" => [
                TOKEN::ARROW['name']
            ],
            "functiondefinition_" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name'],
                TOKEN::IDENTIFIER['name']
            ],
            "divisibilityoperator" => [
                TOKEN::DIVIDES['name'],
                TOKEN::DOESNOTDIVIDE['name']
            ],
            "comparsionoperator" => [
                TOKEN::EQUAL['name'],
                TOKEN::LESSTHAN['name'],
                TOKEN::GREATERTHAN['name'],
                TOKEN::LESSTHANOREQUAL['name'],
                TOKEN::GREATERTHANOREQUAL['name']
            ],
            "logicalrhs" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name'],
                TOKEN::IDENTIFIER['name']
            ],
            "logicalrhs_" => [
                TOKEN::PLUS['name'],
                TOKEN::MINUS['name'],
                TOKEN::MULTIPLY['name'],
                TOKEN::DIVIDE['name'],
                TOKEN::LAND['name'],
                TOKEN::LOR['name'],
                TOKEN::RIGHTCURLYBRACE['name'],
                TOKEN::RIGHTPARENTHESIS['name']
            ],
            "logicalrhs__" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name'],
                TOKEN::IDENTIFIER['name']
            ],
            "logicalexp_" => [
                TOKEN::LAND['name'],
                TOKEN::LOR['name'],
                TOKEN::RIGHTCURLYBRACE['name'],
                TOKEN::RIGHTPARENTHESIS['name']
            ],
            "logicaloperator" => [
                TOKEN::LAND['name'],
                TOKEN::LOR['name']
            ],
            "setliteral" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name']
            ],
            "setliteral_" => [
                TOKEN::COMMA['name'],
                TOKEN::RIGHTCURLYBRACE['name']
            ],
            "pointsetliteral" => [
                TOKEN::LEFTSQUAREBRACKET['name'],
                TOKEN::RIGHTCURLYBRACE['name']
            ],
            "pointsetliteral_" => [
                TOKEN::COMMA['name'],
                TOKEN::RIGHTCURLYBRACE['name']
            ],
            "sexpr" => [
                TOKEN::LEFTCURLYBRACE['name'],
                TOKEN::IDENTIFIER['name'],
                TOKEN::TOBEEQUAL['value'],
                TOKEN::COMPLEMENT['value'],
                TOKEN::DOT['value'],
                TOKEN::EQUAL['value'],
                TOKEN::SUBSETOF['value'],
                TOKEN::REALSUBSETOF['value'],
                TOKEN::UNION['value'],
                TOKEN::INTERSECTION['value'],
                TOKEN::SETMINUS['value'],
                TOKEN::LEFTPARENTHESIS['name'],
                TOKEN::LEFTSQUAREBRACKET['name']
            ],
            "sexpr_" => [
                TOKEN::TOBEEQUAL['name'],
                TOKEN::EQUAL['name'],
                TOKEN::SUBSETOF['name'],
                TOKEN::REALSUBSETOF['name'],
                TOKEN::COMPLEMENT['name'],
                TOKEN::UNION['name'],
                TOKEN::INTERSECTION['name'],
                TOKEN::SETMINUS['name'],
                TOKEN::VERTICALLINE['name'],
                TOKEN::EOL['name'],
                TOKEN::DOT['name']
            ],
            "stesruisc" => [
                TOKEN::TOBEEQUAL['name'],
                TOKEN::EQUAL['name'],
                TOKEN::SUBSETOF['name'],
                TOKEN::REALSUBSETOF['name'],
                TOKEN::COMPLEMENT['name'],
                TOKEN::UNION['name'],
                TOKEN::INTERSECTION['name'],
                TOKEN::SETMINUS['name'],
                TOKEN::VERTICALLINE['name'],
                TOKEN::EOL['name'],
                TOKEN::LEFTPARENTHESIS['name']
            ],
            "trhs" => [
                TOKEN::LEFTCURLYBRACE['name'],
                TOKEN::IDENTIFIER['name'],
                TOKEN::LEFTPARENTHESIS['name'],
                TOKEN::LEFTSQUAREBRACKET['name']
            ],
            "stesruisc_" => [
                TOKEN::LEFTCURLYBRACE['name'],
                TOKEN::IDENTIFIER['name'],
                TOKEN::LEFTPARENTHESIS['name'],
                TOKEN::RIGHTPARENTHESIS['name'],
                TOKEN::VERTICALLINE['name'],
                TOKEN::EOL['name']
            ],
            "stesruisc__" => [
                TOKEN::UNION['name'],
                TOKEN::INTERSECTION['name'],
                TOKEN::SETMINUS['name'],
                TOKEN::COMPLEMENT['name'],
                TOKEN::RIGHTPARENTHESIS['name'],
                TOKEN::VERTICALLINE['name'],
                TOKEN::EOL['name']
            ],
            "uisc" => [
                TOKEN::UNION['name'],
                TOKEN::INTERSECTION['name'],
                TOKEN::SETMINUS['name'],
                TOKEN::COMPLEMENT['name'],
                TOKEN::RIGHTPARENTHESIS['name'],
                TOKEN::VERTICALLINE['name'],
                TOKEN::EOL['name']
            ],
            "sofunctioncall" => [
                TOKEN::DOT['name']
            ],
            "sofunctionname" => [
                TOKEN::ADD['name'],
                TOKEN::DELETE['name']
            ],
            "arguments" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name'],
                TOKEN::IDENTIFIER['name'],
                TOKEN::LEFTCURLYBRACE['name']
            ],
            "argument" => [
                TOKEN::MINUS['name'],
                TOKEN::NUMBER['name'],
                TOKEN::LEFTCURLYBRACE['name'],
                TOKEN::IDENTIFIER['name']
            ],
            "arguments_" => [
                TOKEN::COMMA['name'],
                TOKEN::RIGHTPARENTHESIS['name']
            ],
            "point" => [
                TOKEN::LEFTSQUAREBRACKET['name']
            ],
            "ssimpleexpression" => [
                TOKEN::VERTICALLINE['name']
            ],
            "scardinality" => [
                TOKEN::VERTICALLINE['name']
            ],
            "scardinality_" => [
                TOKEN::LEFTCURLYBRACE['name'],
                TOKEN::IDENTIFIER['name'],
                TOKEN::LEFTPARENTHESIS['name']
            ],
            "ssimpleexpression_" => [
                TOKEN::PLUS['name'],
                TOKEN::MINUS['name'],
                TOKEN::MULTIPLY['name'],
                TOKEN::DIVIDE['name'],
                TOKEN::EOL['name']
            ],
            "simpleoperator" => [
                TOKEN::PLUS['name'],
                TOKEN::MINUS['name'],
                TOKEN::MULTIPLY['name'],
                TOKEN::DIVIDE['name']
            ],
            "sfunctioncall" => [
                TOKEN::VENN['name'],
                TOKEN::POINTSETDIAGRAM['name']
            ],
            "sfunctionname" => [
                TOKEN::POINTSETDIAGRAM['name'],
                TOKEN::VENN['name']
            ],
        ];


        return $possibletokens[$nonterminal];
    }


}
