<?php

namespace core\lib;


use core\lib\exception\DividedByZeroException;
use core\lib\exception\WrongArgumentException;
use core\parser\Token;
use core\parser\Parser;
use core\parser\exception\UndefinedVariableException;
use core\lib\datastructures\Set;
use core\lib\datastructures\Map;
use core\lib\datastructures\Point;

use core\lib\venndiagrams\Venn1;
use core\lib\venndiagrams\Venn2;
use core\lib\venndiagrams\Venn3;

use core\lib\exception\LibException;
use \core\Regexp;
use utils\Lang;
use \utils\Rootfolder;

/**
* A class that defines some utility functions for working with sets and numbers.
*
* This class contains static methods that perform various operations on sets and numbers,
* such as checking types, creating sets, calculating differences, unions, intersections, etc.
* It also contains some methods that process logical expressions and create divisibility conditions.
* @package core\lib;
*/
class Functions
{
    /**
    * An array of colors for the Venn diagram.
    */
    private static $colorpalette=[];

    /**
    * Throws an exception for  arguments for a function.
    *
    * @param string $functionName The name of the function that received  arguments.
    * @throws LibException
    * @public
    */
    public static function illegalArguments($functionName){
        $errorMessagesLang=Parser::getLang()?:'eng';
        return new WrongArgumentException(Lang::getString('invalidArgumentsError',$errorMessagesLang). $functionName);
    }

    /**
    * Checks if an element is a number.
    *
    * @param mixed $element The element to check.
    * @return bool True if the element is a number, false otherwise.
    * @public
    */
    public static function isNumber($element){
        return is_numeric($element);
    }

    /**
    * Checks if a literal is a string.
    *
    * @param mixed $literal The literal to check.
    * @return bool True if the literal is a string, false otherwise.
    * @public
    */
    public static function isString($literal){
        return is_string($literal);
    }

    /**
    * Checks if an array is an array.
    *
    * @param mixed $array The array to check.
    * @return bool True if the array is an array, false otherwise.
    * @public
    */
    public static function isArray($array){
        return is_array($array);
    }

    /**
    * Checks if an array is empty.
    *
    * @param mixed $array The array to check.
    * @return bool True if the array is empty, false otherwise.
    * @public
    */
    public static function isEmptyArray($array){
        return Functions::isArray($array)&& empty($array);
    }

    /**
    * Checks if an array is not empty.
    *
    * @param mixed $array The array to check.
    * @return bool True if the array is not empty, false otherwise.
    * @public
    */
    public static function isNotEmptyArray($array){
        return !Functions::isEmptyArray($array);
    }

    /**
    * Checks if an object is an object.
    *
    * @param mixed $object The object to check.
    * @return bool True if the object is an object, false otherwise.
    * @public
    */
    public static function isObject($object){
        return is_object($object);
    }

    /**
    * Checks if a user-defined function is a function.
    *
    * @param mixed $userDefinedFunction The user-defined function to check.
    * @return bool True if the user-defined function is a function, false otherwise.
    * @public
    */
    public static function isFunction($userDefinedFunction){
        return is_callable($userDefinedFunction);
    }

    /**
    * Checks if an element is null.
    *
    * @param mixed $element The element to check.
    * @return bool True if the element is null, false otherwise.
    * @public
    */
    public static function isNull($element){
        return is_null($element);
    }

    /**
    * Checks if an element is not null.
    *
    * @param mixed $element The element to check.
    * @return bool True if the element is not null, false otherwise.
    * @public
    */
    public static function isNotNull($element){
        return !Functions::isNull($element);
    }

    /**
    * Checks if an element is a whole number (integer).
    *
    * @param mixed $element The element to check.
    * @return bool True if the element is a whole number, false otherwise.
    * @public
    */
    public static function isWholeNumber($element){
        $regexp=new Regexp('^(0|[-]?[1-9][0-9]*)$');
        return Functions::isNumber($element)&&$regexp->test($element);
    }

    /**
    * Checks if a set is a set.
    *
    * @param mixed $set The set to check.
    * @return bool True if the set is a set, false otherwise.
    * @public
    */
    public static function isSet($set){
        return gettype($set)==="object"&&$set instanceof Set;
    }

    /**
    * Checks if a set is a set.
    *
    * @param mixed $array The array to check.
    * @return bool True if the set is a set, false otherwise.
    * @public
    */
    public static function isSetArray($sets){
        if (!Functions::isArray($sets)) return false;
        foreach ($sets as $set) {
            if (!Functions::isSet($set)) return false;
        }
        return true;
    }

    /**
    * Checks if a map is a map.
    *
    * @param mixed $map The map to check.
    * @return bool True if the map is a map, false otherwise.
    * @public
    */
    public static function isMap($map){
        return gettype($map)==="object"&&$map instanceof Map;
    }

    /**
    * Checks if an operation is a good operation for the given array of good operations.
    *
    * @param string $operation The operation to check.
    * @param array $goodoperations The array of good operations to compare with.
    * @return bool True if the operation is a good operation, false otherwise.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function isGoodOperation($operation, $goodoperations){
        if(!Functions::isString($operation)||!Functions::isArray($goodoperations)) return false;
        return in_array($operation,$goodoperations);
    }

    /**
    * Removes null elements from an array.
    *
    * @param array $array The array to remove null elements from.
    * @return array A new array without null elements.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function removeNullFromArray($array){
        if(!Functions::isArray($array)) throw Functions::illegalArguments(__METHOD__);
        return array_values(array_filter($array,__CLASS__.'::isNotNull'));
    }
    
    /**
    * Removes empty arrays from an array.
    *
    * @param array $array The array to remove empty arrays from.
    * @return array A new array without empty arrays.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function removeEmptyArrayFromArray($array){
        if(!Functions::isArray($array)) throw Functions::illegalArguments(__METHOD__);
        return array_values(array_filter($array,__CLASS__.'::isNotEmptyArray'));
    }

    /**
    * Creates a set from an array of elements.
    *
    * @param array $array The array of elements to create a set from.
    * @return Set A new set with the elements from the array.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function createSetFromArray($array){
        if(!Functions::isArray($array)) throw Functions::illegalArguments(__METHOD__);
        $result= new Set([]);
        foreach ($array as $value) {
            $result->add($value);
        }
        return $result->orderByAsc();

    }

    /**
    * Creates a base set from a map of sets.
    *
    * @param Map $map The map of sets to create a base set from.
    * @return Set A new set that is the union of all the sets in the map.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function createBaseSet($map){
        if(!Functions::isMap($map)) throw Functions::illegalArguments(__METHOD__);
        $sets=array_filter($map->values(),__CLASS__.'::isSet');
        return Functions::union(...$sets);
    }

    /**
    * Creates a set from a formula that generates elements in a range and filters them by another formula.
    *
    * @param int $start The start of the range (inclusive).
    * @param int $end The end of the range (inclusive).
    * @param Closure|null $filterformula The formula that filters the elements in the range. Optional, default null.
    * @param Closure|null $formula The formula that generates the elements in the range. Optional, default null.
    * @return Set A new set with the elements generated and filtered by the formulas.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function createSetFromFormula($start,$end,$filterformula=null, $formula=null){
        if(!Functions::isWholeNumber($start) || !Functions::isWholeNumber($end)
        ||(!Functions::isFunction($filterformula)&&!Functions::isNull($filterformula))
        ||(!Functions::isFunction($formula)&&!Functions::isNull($formula))) throw Functions::illegalArguments(__METHOD__);
        $numbers=range($start,$end);
        if($filterformula!==null){
            $numbers=array_filter($numbers,$filterformula);
            $numbers=array_values($numbers);
        }
        if($formula!==null){
            $numbers=array_map($formula,$numbers);
        }
        $numbers=array_filter($numbers,__CLASS__.'::isWholenumber');
        $result=new Set($numbers);
        return $result->orderByAsc();
    }

    /**
    * Checks if a set is empty.
    *
    * @param Set $set The set to check.
    * @return bool True if the set is empty, false otherwise.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function isEmpty($set){
        if(!Functions::isSet($set)) return false;
        return $set->size()===0;
    }

    /**
    * Checks if an element is an element of a set.
    *
    * @param int $element The element to check.
    * @param Set $set The set to check.
    * @return bool True if the element is an element of the set, false otherwise.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function isElementOf($element,$set){
        if(!Functions::isSet($set) || !Functions::isWholeNumber($element)) return false;
        return $set->has($element);
    }

    /**
    * Checks if an element is an element of a set.
    *
    * @param int $element The element to check.
    * @param Set $set The set to check.
    * @return bool True if the element is an element of the set, false otherwise.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function isNotElementOf($element,$set){
        if(!Functions::isSet($set) || !Functions::isWholeNumber($element)) return false;
        return !Functions::isElementOf($element,$set);
    }

    /**
    * Calculates the difference between two sets.
    *
    * @param Set $seta The first set.
    * @param Set $setb The second set.
    * @return Set A new set that contains the elements that are in the first set but not in the second set.
    * @throws LibException If the arguments are not valid sets.
    * @public
    */
    public static function difference($seta,$setb){
        if(!Functions::isSet($seta) || !Functions::isSet($setb)) throw Functions::illegalArguments(__METHOD__);
        $result= new Set([]);
        foreach ($seta as $value) {
            if(!$setb->has($value)){
                $result->add($value);
            }
        }
        return $result->orderByAsc();
    }

    /**
    * Checks if two sets are equal.
    *
    * @param Set $seta The first set.
    * @param Set $setb The second set.
    * @return bool True if the two sets have the same elements, false otherwise.
    * @throws LibException If the arguments are not valid sets.
    * @public
    */
    public static function areEqual($seta,$setb){
        if(!Functions::isSet($seta) || !Functions::isSet($setb)) return false;
        return Functions::isEmpty(Functions::difference($seta,$setb)) && Functions::isEmpty(Functions::difference($setb,$seta));
    }

    /**
    * Checks if a set is a subset of another set.
    *
    * @param Set $seta The subset candidate.
    * @param Set $setb The superset candidate.
    * @return bool True if every element of the first set is also an element of the second set, false otherwise.
    * @throws LibException If the arguments are not valid sets.
    * @public
    */
    public static function isSubsetOf($seta,$setb){
        if(!Functions::isSet($seta) || !Functions::isSet($setb)) return false;
        foreach ($seta as $value) {
            if(!$setb->has($value)){
              return false;
            }
        }
        return true;
    }

    /**
    * Checks if a set is a proper subset of another set.
    *
    * @param Set $seta The proper subset candidate.
    * @param Set $setb The proper superset candidate.
    * @return bool True if the first set is a subset of the second set and they are not equal, false otherwise.
    * @throws LibException If the arguments are not valid sets.
    * @public
    */
    public static function isRealSubsetOf($seta,$setb){
        if(!Functions::isSet($seta) || !Functions::isSet($setb)) return false;
        return Functions::isSubsetOf($seta,$setb) && !Functions::areEqual($seta,$setb);
    }

    /**
    * Calculates the complement of a set with respect to another set.
    *
    * @param Set $set The set to complement.
    * @param Set $universe The set that contains the set to complement.
    * @return Set A new set that contains the elements that are in the universe but not in the set.
    * @throws LibException If the arguments are not valid sets.
    * @public
    */
    public static function complement($set,$universe){
        if(!Functions::isSet($set) || !Functions::isSet($universe)) throw Functions::illegalArguments(__METHOD__);
        return Functions::difference($universe,$set);
    }

    /**
    * Calculates the union of multiple sets.
    *
    * @param array ...$sets The sets to union.
    * @return Set A new set that contains the elements that are in any of the sets.
    * @throws LibException If any of the arguments is not a valid set.
    * @public
    */
    public static function union(...$sets){
        foreach ($sets as $set) {
           if(!Functions::isSet($set)) throw Functions::illegalArguments(__METHOD__);
        }
        $result=new Set([]);
        foreach ($sets as $set) {
            foreach ($set as $value) {
               $result->add($value);
            }
        }
        return $result->orderByAsc();
    }

    /**
    * Calculates the intersection of multiple sets.
    *
    * @param array ...$sets The sets to intersect.
    * @return Set A new set that contains the elements that are in all of the sets.
    * @throws LibException If any of the arguments is not a valid set.
    * @public
    */
    public static function intersection(...$sets){
        foreach ($sets as $set) {
            if(!Functions::isSet($set)) throw Functions::illegalArguments(__METHOD__);
        }
        $result=[...$sets[0]];
        foreach ($sets as $set) {
           $result=array_intersect($result,[...$set]);
        }
        return (new Set($result))->orderByAsc();
    }

    /**
    * Calculates the cardinality (size) of a set.
    *
    * @param Set $set The set to measure.
    * @return int The number of elements in the set.
    * @throws LibException If the argument is not a valid set.
    * @public
    */
    public static function cardinality($set){
        if(!Functions::isSet($set)) throw Functions::illegalArguments(__METHOD__);
        return $set->size();
    }

    /**
    * Adds an element to a set.
    *
    * @param int $element The element to add. 
    * @param Set $set The set to add to. 
    * @return bool True if the element was added successfully, false otherwise. 
    * @throws LibException If the arguments are not valid.
    * @public 
    */
    public static function addElement($element,$set){
        if(!Functions::isSet($set) || !Functions::isWholeNumber($element)) throw Functions::illegalArguments(__METHOD__);
        $oldSize=$set->size();       
        return $set->has($element) || $set->add($element)->orderByAsc()->size()===$oldSize+1;
    }

    /**
    * Deletes an element from a set.
    *
    * @param int $element The element to delete. 
    * @param Set $set The set to delete from. 
    * @return bool True if the element was deleted successfully, false otherwise. 
    * @throws LibException If the arguments are not valid.
    * @public 
    */
    public static function deleteElement($element,$set){
        if(!Functions::isSet($set) || !Functions::isWholeNumber($element)) throw Functions::illegalArguments(__METHOD__);
        $oldSize=$set->size();
        return !$set->has($element) || $set->delete($element)->orderByAsc()->size()===$oldSize-1;
    }

    /**
    * Creates a divisibility condition for a number
    *
    * @param int $number The number to check divisibility for. 
    * @param string $operation The operation to use for divisibility. Can be either Token::DIVIDE['value'] or Token::DOESNOTDIVIDE['value'].
    * @return Closure A function that takes a number as an argument and returns true if it satisfies the divisibility condition, false otherwise. 
    * @throws LibException If the arguments are not valid.
    * @public 
    */
    public static function createDivisibilityCondition($number, $operation){
        if(!Functions::isWholeNumber($number)||!Functions::isGoodOperation($operation,array(Token::DIVIDES['value'],Token::DOESNOTDIVIDE['value']))) throw Functions::illegalArguments(__METHOD__);
        return $operation===Token::DIVIDES['value']? function($num) use($number){ return $num%$number===0;}:function($num)use($number){ return $num%$number!==0;};
    }

    /**
    * Processes the right-hand side of a logical expression and returns an array of functions.
    *
    * @param array $rhsparts The right-hand side parts of the logical expression. 
    * @return array An associative array of functions that can be applied to the right-hand side variables or constants.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function processLogicalRhs($rhsparts){
        $rhsfuncs=[];
        if(!Functions::isArray($rhsparts)) throw Functions::illegalArguments(__METHOD__);
        if(isset($rhsparts['num'])&&isset($rhsparts['simpleop'])&&isset($rhsparts['id'])){
            if(!Functions::isWholeNumber($rhsparts['num'])||!Functions::isGoodOperation($rhsparts['simpleop'],
            array(Token::PLUS['value'],Token::MINUS['value'],Token::MULTIPLY['value'],Token::DIVIDE['value']))) throw Functions::illegalArguments(__METHOD__);
            switch ($rhsparts['simpleop']) {
                case (Token::PLUS['value']):
                    $rhsfuncs[$rhsparts['id']][]=function($var) use($rhsparts) {return $var + $rhsparts['num']; };
                    $rhsfuncs['num']=$rhsparts['num'];
                    break;
                case (Token::MINUS['value']):
                    $rhsfuncs[$rhsparts['id']][]=function($var) use($rhsparts) {return $var - $rhsparts['num']; };
                    $rhsfuncs['num']=$rhsparts['num'];
                    break;
                case (Token::MULTIPLY['value']):
                    $rhsfuncs[$rhsparts['id']][]=function($var) use($rhsparts) {return $var * $rhsparts['num']; };
                    $rhsfuncs['num']=$rhsparts['num'];
                    break;
                case (Token::DIVIDE['value']):
                    if($rhsparts['num']===0){
                        $errorMessagesLang=Parser::getLang()?:'eng';
                        throw new DividedByZeroException(Lang::getString('dividedByZeroInBoundaryFunctionError',$errorMessagesLang));
                    }
                    $rhsfuncs[$rhsparts['id']][]=function($var) use($rhsparts) {return $var / $rhsparts['num']; };
                    $rhsfuncs['num']=$rhsparts['num'];
                    break;
            }
        }
        else if(isset($rhsparts['num'])&&isset($rhsparts['simpleop'])&&Functions::isArray($rhsparts['num'])){
            switch ($rhsparts['simpleop']) {
                case (Token::PLUS['value']):
                    $constant['num']=$rhsparts['num'][0]+$rhsparts['num'][1];
                    $rhsfuncs['constant']=function() use($constant) { return $constant['num']; };
                    $rhsfuncs['num']=$constant['num'];
                    break;
                case (Token::MINUS['value']):
                    $constant['num']=$rhsparts['num'][0]-$rhsparts['num'][1];
                    $rhsfuncs['constant']=function() use($constant) { return $constant['num']; };
                    $rhsfuncs['num']=$constant['num'];
                    break;
                case (Token::MULTIPLY['value']):
                    $constant['num']=$rhsparts['num'][0]*$rhsparts['num'][1];
                    $rhsfuncs['constant']=function() use($constant) { return $constant['num']; };
                    $rhsfuncs['num']=$constant['num'];
                    break;
                case (Token::DIVIDE['value']):
                    if($rhsparts['num'][1]===0){
                        $errorMessagesLang=Parser::getLang()?:'eng';
                        throw new DividedByZeroException(Lang::getString('dividedByZeroInBoundaryFunctionError',$errorMessagesLang));
                    }
                    $constant['num']=$rhsparts['num'][0]/$rhsparts['num'][1];
                    $rhsfuncs['constant']=function() use($constant) { return $constant['num']; };
                    $rhsfuncs['num']=$constant['num'];
                    break;
            }
        }
        else if (isset($rhsparts['num'])) {
            $rhsfuncs['constant']=function () use ($rhsparts) {return $rhsparts['num']; };
            $rhsfuncs['num']=$rhsparts['num'];
        }
        return $rhsfuncs;
    }

    /**
    * Creates a comparison condition for a comparison operator and an array of logical right-hand side functions.
    *
    * @param string $comparsionop The comparison operator to use. Can be one of the Token constants: LESSTHAN, GREATERTHAN, LESSTHANOREQUAL, GREATERTHANOREQUAL, or EQUAL.
    * @param array $logicalrhsfuncs An associative array of functions that can be applied to the right-hand side variables or constants.
    * @return Closure A function that takes a variable as an argument and returns true if it satisfies the comparison condition, false otherwise.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function createComparsionCondition($comparsionop,$logicalrhsfuncs){
        if(!Functions::isString($comparsionop)||!Functions::isArray($logicalrhsfuncs)) throw Functions::illegalArguments(__METHOD__);
        switch ($comparsionop) {
            case (Token::LESSTHAN['value']):
                $compCond=function($var) use($logicalrhsfuncs) {$constantrhs=$logicalrhsfuncs['constant'];return $var <  $constantrhs();};
                break;
            case (Token::GREATERTHAN['value']):
                $compCond=function($var) use($logicalrhsfuncs) {$constantrhs=$logicalrhsfuncs['constant'];return $var > $constantrhs(); };
                break;
            case (Token::LESSTHANOREQUAL['value']):
                $compCond=function($var) use($logicalrhsfuncs) {$constantrhs=$logicalrhsfuncs['constant'];return $var <= $constantrhs(); };
                break;
            case (Token::GREATERTHANOREQUAL['value']):
                $compCond=function($var) use($logicalrhsfuncs) {$constantrhs=$logicalrhsfuncs['constant'];return $var >= $constantrhs(); };
                break;
            case (Token::EQUAL['value']):
                $compCond=function($var) use($logicalrhsfuncs) {$constantrhs=$logicalrhsfuncs['constant'];return $var == $constantrhs(); };
                break;
        }
        return $compCond;
    }

    /**
    * Creates a user-defined function for a simple arithmetic operator and a number.
    *
    * @param string $simpleop The simple arithmetic operator to use. Can be one of the Token constants: PLUS, MINUS, MULTIPLY, or DIVIDE.
    * @param int $num The number to use for the arithmetic operation.
    * @return Closure A function that takes a variable as an argument and returns the result of applying the arithmetic operation with the number.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function createUserFunction($simpleop,$num){
        if(!Functions::isWholeNumber($num)||!Functions::isGoodOperation($simpleop, array(Token::PLUS['value'],Token::MINUS['value'],
        Token::MULTIPLY['value'],Token::DIVIDE['value']))) throw Functions::illegalArguments(__METHOD__);
        switch ($simpleop) {
            case (Token::PLUS['value']):
                $userfunc=function($var) use($num) {return $var + $num; };
                break;
            case (Token::MINUS['value']):
                $userfunc=function($var) use($num) {return $var - $num; };
                break;
            case (Token::MULTIPLY['value']):
                $userfunc=function($var) use($num) {return $var * $num; };
                break;
            case (Token::DIVIDE['value']):
                if($num===0){
                    throw new DividedByZeroException(Lang::getString('dividedByZeroInUserDefindedFunctionError',Parser::getLang()));
                }
                $userfunc=function($var) use($num) {return $var / $num; };
                break;
        }
        return $userfunc;

    }

    /**
    * Transforms a set of conditions to be concatenated into a tree structure.
    *
    * @param array $partsToConcat The parts of the conditions to be concatenated. 
    * @return array An associative array that represents the tree structure of the conditions. 
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function transformConditionsToTree($partsToConcat){
        if(!Functions::isArray($partsToConcat)) throw Functions::illegalArguments(__METHOD__);
        $varname=array_diff(array_keys($partsToConcat),array('logicalop'));
        if(isset($partsToConcat['logicalop'])){
            if(!Functions::isGoodOperation($partsToConcat['logicalop'],
            array(Token::LAND['value'],Token::LOR['value']))) throw Functions::illegalArguments(__METHOD__);
            
            if(isset($partsToConcat['subexp'])) {
                switch ($partsToConcat['logicalop']) {
                    case (Token::LAND['value']):
                        $result['&&']=$partsToConcat['subexp'];
                        break;
                    case (Token::LOR['value']):
                        $result['||']=$partsToConcat['subexp'];
                        break;
                }
            }            
        }
        else if(isset($partsToConcat['subexp'])){
            $restparts=$partsToConcat['subexp'];
            if(!(array_search('(',$restparts)!==false&&array_search(')',$restparts)!==false)){
                $varname=array_diff(array_keys($restparts),array('logicalop'))[0];
                $op= array_keys($restparts[$varname])[0];
                $result[$varname][$op]=$restparts[$varname][$op];
                if(isset($restparts[$varname]['bound'])){
                    $result[$varname]['bound']=$restparts[$varname]['bound'];
                }
            }
            else{
               return $restparts; 
            }
        }
        else{
            $varname=array_diff(array_keys($partsToConcat),array('logicalop'))[0];
            $result=$partsToConcat[$varname][0];
        }
        return $result;
    }

    /**
    * Collects the bound values from an object.
    *
    * @param array $obj The object to collect the bound values from. 
    * @return array An array of the bound values. 
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function collectBounds($obj) {
        if(!Functions::isArray($obj)) throw Functions::illegalArguments(__METHOD__);
        $bounds = array(); 
        foreach ($obj as $prop => $value){
            if(strpos($prop,'boundfunc')!==false){
                $keys=array_filter(array_keys($value),function ($value) {
                    return str_ends_with($value,'boundvalue');
                });
                $key=reset($keys);
                if(Functions::isArray($value[$key])){
                    $funckey=str_replace('boundvalue','boundfunc',$key);
                    $func=$value[$funckey];
                    $value[$key]=function ($var) use ($func) { return $func($var); };
                }
                $bounds[]=$value[$key];
            }
        }
        return $bounds;
    }

    /**
    * Collects the bound functions from an object.
    *
    * @param array $obj The object to collect the bound functions from. 
    * @return array An array of the bound functions. 
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function collectBoundFuncs($obj) {
        if(!Functions::isArray($obj)) throw Functions::illegalArguments(__METHOD__);
        $boundfuncs = array(); 
        foreach ($obj as $key=>$value){
            if((strpos($key,'boundfunc')!==false&&Functions::isArray($value))||(strpos($key,'dividesfunc')!==false&&Functions::isArray($value))){
                $closure=array_filter($value,__CLASS__.'::isFunction');
                $boundfuncs[]=reset($closure);
            }
            else if((strpos($key,'&&')!==false||strpos($key,'||')!==false||strpos($key,'(')!==false)||strpos($key,')')!==false){
                $boundfuncs[]=$value;
            }
        }
        if(end($boundfuncs)==='&&'||end($boundfuncs)==='||'){
            array_pop($boundfuncs);
        }
        return $boundfuncs;
    }

    /**
    * Removes any duplicated operators from an array of operators.
    *
    * This function iterates over an array of operators and removes any consecutive duplicates. 
    * It returns a new array with the unique operators.
    *
    * @param array $arr The array of operators to process.
    * @return array A new array with the duplicated operators removed.
    * @throws LibException If the $arr parameter is not an array.
    */

    public static function removeDuplicatedOperator($arr){
        for ($i=0; $i <count($arr) ; $i++) { 
           if($i>0&&$arr[$i-1]===$arr[$i]){
                unset($arr[$i]);
                $arr=array_values($arr);
           }
        }
        return $arr;
    }
   
    /**
    * Concatenates the bound conditions with logical operators and returns a single function.
    *
    * @param array $boundfuncswithop The bound functions with logical operators to concatenate. 
    * @return Closure A function that takes a variable as an argument and returns true if it satisfies the concatenated bound conditions, false otherwise. 
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function concatBoundConditions($boundfuncswithop){
        if(!Functions::isArray($boundfuncswithop)) throw Functions::illegalArguments(__METHOD__);
        if(!(array_search('(',$boundfuncswithop)!==false&&array_search(')',$boundfuncswithop)!==false)){        
            while ($index=array_search("&&",$boundfuncswithop)) {
                $indexbefore=$index-1;
                $indexafter=$index+1;
                $boundfuncswithop[$indexbefore]=function ($x) use ($boundfuncswithop,$indexbefore,$indexafter) {
                    return $boundfuncswithop[$indexbefore]($x)&&$boundfuncswithop[$indexafter]($x);
                };
                unset($boundfuncswithop[$index]);
                unset($boundfuncswithop[$indexafter]);
                $boundfuncswithop=array_values($boundfuncswithop);
            }
            while ($index=array_search("||",$boundfuncswithop)) {
                $indexbefore=$index-1;
                $indexafter=$index+1;
                $boundfuncswithop[$indexbefore]=function ($x) use ($boundfuncswithop,$indexbefore,$indexafter) {
                    return $boundfuncswithop[$indexbefore]($x)||$boundfuncswithop[$indexafter]($x);
                };
                unset($boundfuncswithop[$index]);
                unset($boundfuncswithop[$indexafter]);
                $boundfuncswithop=array_values($boundfuncswithop);
            }
            return $boundfuncswithop[0];
        }
        else{
            while (array_search(')',$boundfuncswithop)!==false) {
                $rightparent=array_search(')',$boundfuncswithop);
                $leftparent= array_search('(',array_reverse($boundfuncswithop,true));
                $subexp=array_slice($boundfuncswithop,$leftparent+1,($rightparent-$leftparent)-1);
                $subresult=Functions::concatBoundConditions($subexp);
                $boundfuncswithop=array_merge(array_slice($boundfuncswithop,0,$leftparent),[$subresult],array_slice($boundfuncswithop,$rightparent+1));
            }
            return Functions::concatBoundConditions($boundfuncswithop);
        }
    }

    /**
    * Gets the function definition from an object.
    *
    * @param array $obj The object to get the function definition from.
    * @return Closure|null The function definition as a closure, or null if not found.
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function getFuncDef($obj){
        if(!Functions::isArray($obj)) throw Functions::illegalArguments(__METHOD__);
       $key=array_filter(array_keys($obj),function ($var)  {
             return str_contains($var,'funcdef');
        });
        if(empty($key)) return null;
        $key=reset($key);
        return reset($obj[$key]);
    }

    /**
    * Checks if the variables are valid for the function definition.
    *
    * @param array $vars The variables to check.
    * @return bool True if the variables are valid, false otherwise.
    * @public
    */
    public static function isVariablesGood($vars){
       $id=array_filter($vars,function ($var)  {
            return str_contains($var,'0');
        });
        if(empty($id)) return false;
        $id=reset($id);
        $id=explode('_',$id)[1];
        $boundfunc=array_filter($vars,function ($var)  {
            return str_contains($var,'boundfunc');
        });
        if(empty($boundfunc)) return false;
        $boundvar=explode('_',reset($boundfunc))[1];
        $filtered = array_filter ($boundfunc, function ($var) use($boundvar) {
            return str_contains($var,$boundvar);
        });
        if(count ($filtered) !== count ($boundfunc)) return false;
        $dividesfunc=array_filter($vars,function ($var)  {
            return str_contains($var,'dividesfunc');
        });
        $filtered = array_filter ($dividesfunc, function ($var) use($boundvar) {
            return str_contains($var,$boundvar);
        });
        if(!empty($dividesfunc)&&(count ($filtered) !== count ($dividesfunc))) return false;
        $funcdef=array_filter($vars,function ($var)  {
            return str_contains($var,'funcdef');
        });
        if(empty($funcdef)) return true;
        $funcdefvars=explode('_',reset($funcdef));
        if($id!==$funcdefvars[1]||$boundvar!==$funcdefvars[2]) return false;
        
        return true;
    }

    /**
    * Gets the minimum and maximum values from an array.
    *
    * @param array $array The array to get the minimum and maximum values from.
    * @return array An associative array with keys 'start' and 'end' that contain the minimum and maximum values respectively.
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function getMinMax($array){
        if(!Functions::isArray($array)) throw Functions::illegalArguments(__METHOD__);
        return [
            'start'=>min($array),
            'end'=>max($array)
        ];
    }

    /**
    * Evaluates a set expression and returns the result.
    *
    * @param array $array The array that represents the set expression. It can contain sets, operators, or parentheses.
    * @return mixed The result of evaluating the set expression. It can be a set, a boolean, or null if the expression is invalid.
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function evaluateSetExpression($array){
        if(!Functions::isArray($array)) throw Functions::illegalArguments(__METHOD__);
        if(!(array_search('(',$array)!==false&&array_search(')',$array)!==false)){
            
            if(count($array)==1){
                return $array[0];
            }

            $separated=Functions::separateOperandsAndOperations($array);
            $sets=$separated['sets'];
            $operations=$separated['operations'];

            $lastSetIndex=max(array_keys($sets));
            $lastOperationIndex=max(array_keys($operations));

            if(count($operations)>1){
                while ($index=array_search(Token::COMPLEMENT['value'],$operations)) {
                    if(Parser::getBaseSet()===null){
                        throw new UndefinedVariableException(Lang::getString('baseSetNotDefinedError',Parser::getLang()));
                    }
                    $result=Functions::complement($sets[$index-1],Parser::getBaseSet());
                    $sets[$index-1]=$result;
                    unset($operations[$index]);
                }
                if(!empty($sets)){
                    $lastSetIndex=max(array_keys($sets));
                }
                if(!empty($operations)){
                    $lastOperationIndex=max(array_keys($operations));
                }
            }



            if(count($operations)>1){
                while ($index=array_search(Token::INTERSECTION['value'],$operations)) {
                    $result=Functions::intersection($sets[$index-1],$sets[$index+1]);
                    $sets[$index-1]=$result;
                    $lastSetIndex=max(array_keys($sets));
                    $lastOperationIndex=max(array_keys($operations));
                    for ($i=$index+1; $i <$lastSetIndex ; $i+=2) { 
                        $sets[$i]=$sets[$i+2];
                    }
                    for ($i=$index; $i <$lastOperationIndex ; $i+=2) { 
                        $operations[$i]=$operations[$i+2];
                    }
                    unset($sets[$lastSetIndex]);
                    unset($operations[$lastOperationIndex]);
                }
                if(!empty($sets)){
                    $lastSetIndex=max(array_keys($sets));
                }
                if(!empty($operations)){
                    $lastOperationIndex=max(array_keys($operations));
                }
            }
            
            if(count($operations)>1){
                for ($i=1; $i <= $lastOperationIndex; $i+=2) { 
                    $i=1;
                    switch ($operations[$i]) {
                        case (Token::SETMINUS['value']):
                            $result=Functions::difference($sets[$i-1],$sets[$i+1]);
                            break;
                        case (Token::UNION['value']):
                            $result=Functions::union($sets[$i-1],$sets[$i+1]);
                            break;                  
                    }
                    $sets[$i-1]=$result;
                    $lastSetIndex=max(array_keys($sets));
                    $lastOperationIndex=max(array_keys($operations));
                    for ($j=$i+1; $j <$lastSetIndex ; $j+=2) { 
                        $sets[$j]=$sets[$j+2];
                    }
                    for ($j=$i; $j <$lastOperationIndex ; $j+=2) { 
                        $operations[$j]=$operations[$j+2];
                    }
                    unset($sets[$lastSetIndex]);
                    unset($operations[$lastOperationIndex]);
                    if(!empty($sets)){
                        $lastSetIndex=max(array_keys($sets));
                    }
                    if(!empty($operations)){
                        $lastOperationIndex=max(array_keys($operations));
                    }
                    else{
                        break;
                    }
                }
            }
            if(!empty($operations)){
                switch ($operations[array_key_first($operations)]) {
                    case (Token::EQUAL['value']):
                        $result = Functions::areEqual($sets[array_key_first($sets)],$sets[array_key_last($sets)]);
                        break;
                    case (Token::SUBSETOF['value']):
                        $result = Functions::isSubsetOf($sets[array_key_first($sets)],$sets[array_key_last($sets)]);
                        break;
                    case (Token::REALSUBSETOF['value']):
                        $result = Functions::isRealSubsetOf($sets[array_key_first($sets)],$sets[array_key_last($sets)]);
                        break;
                    case (Token::SETMINUS['value']):
                        $result=Functions::difference($sets[array_key_first($sets)],$sets[array_key_last($sets)]);
                        break;
                    case (Token::UNION['value']):
                        $result=Functions::union($sets[array_key_first($sets)],$sets[array_key_last($sets)]);
                        break;
                    case (Token::INTERSECTION['value']):
                        $result=Functions::intersection($sets[array_key_first($sets)],$sets[array_key_last($sets)]);
                        break;
                    case (Token::COMPLEMENT['value']):
                        if(Parser::getBaseSet()===null){
                            throw new UndefinedVariableException(Lang::getString('baseSetNotDefinedError',Parser::getLang()));
                        }
                        $result=Functions::complement($sets[array_key_first($sets)],Parser::getBaseSet());
                        break;   
                }
    
            }
            
           return $result;
        }
        else{
            while (array_search(')',$array)!==false) {
                $rightparent=array_search(')',$array);
                $leftparent= array_search('(',array_reverse($array,true));
                $subexp=array_slice($array,$leftparent+1,($rightparent-$leftparent)-1);
                $subresult=Functions::evaluateSetExpression($subexp);
                $array=array_merge(array_slice($array,0,$leftparent),[$subresult],array_slice($array,$rightparent+1));
            }
            return Functions::evaluateSetExpression($array);
           
            
        }
    }

    /**
    * Evaluates a simple arithmetic expression from an array of tokens.
    *
    * This function takes an array of tokens that represent a simple arithmetic expression, such as [2, "+", 3, "*", 4].
    * It assumes that the tokens are valid and follow the order of operations and parentheses rules.
    * It returns the result of the expression as a numeric value.
    *
    * @param array $array The array of tokens to evaluate.
    * @return numeric The result of the expression.
    * @throws LibException If the $array parameter is not an array or is empty.
    */
    public static function evaluateSimpleExpression($array){
        if(!Functions::isArray($array)) throw Functions::illegalArguments(__METHOD__);
        if(count($array)==1){
            return $array[0];
        }

        $separated=Functions::separateOperandsAndOperations($array);
        $numbers=$separated['numbers'];
        $operations=$separated['operations'];
        $lastOperationIndex=max(array_keys($operations));
        if(count($operations)>1){
            while ($index=Functions::arraySearchMinIndex(array_search(Token::MULTIPLY['value'],$operations),array_search(Token::DIVIDE['value'],$operations))) {
                    switch ($operations[$index]) {
                        case (Token::MULTIPLY['value']):
                            $result=$numbers[$index-1]*$numbers[$index+1];
                            break;
                        case (Token::DIVIDE['value']):
                            $result=$numbers[$index+1]!==0?$numbers[$index-1]/$numbers[$index+1]:$numbers[$index-1];
                            break; 
                    }
                    $numbers[$index-1]=$result;
                    $lastNumeberIndex=max(array_keys($numbers));
                    $lastOperationIndex=max(array_keys($operations));
                    for ($j=$index+1; $j <$lastNumeberIndex ; $j+=2) { 
                        $numbers[$j]=$numbers[$j+2];
                    }
                    for ($j=$index; $j <$lastOperationIndex ; $j+=2) { 
                        $operations[$j]=$operations[$j+2];
                    }
                    unset($numbers[$lastNumeberIndex]);
                    unset($operations[$lastOperationIndex]);                 
            }
        }
            
        if(count($operations)>1){
            for ($i=1; $i <= $lastOperationIndex; $i+=2) { 
                $i=1;
                switch ($operations[$i]) {
                    case (Token::MINUS['value']):
                        $result=$numbers[$i-1]-$numbers[$i+1];
                        break;
                    case (Token::PLUS['value']):
                        $result=$numbers[$i-1]+$numbers[$i+1];
                        break;                  
                }
                $numbers[$i-1]=$result;
                $lastNumeberIndex=max(array_keys($numbers));
                $lastOperationIndex=max(array_keys($operations));
                for ($j=$i+1; $j <$lastNumeberIndex ; $j+=2) { 
                    $numbers[$j]=$numbers[$j+2];
                }
                for ($j=$i; $j <$lastOperationIndex ; $j+=2) { 
                    $operations[$j]=$operations[$j+2];
                }
                unset($numbers[$lastNumeberIndex]);
                unset($operations[$lastOperationIndex]);
                if(!empty($numbers)){
                    $lastNumeberIndex=max(array_keys($numbers));
                }
                if(!empty($operations)){
                    $lastOperationIndex=max(array_keys($operations));
                }
                else{
                    break;
                }
            }
        }
        
        switch ($operations[array_key_first($operations)]) {
            case (Token::PLUS['value']):
                $result = $numbers[array_key_first($numbers)]+$numbers[array_key_last($numbers)];
                break;
            case (Token::MINUS['value']):
                $result = $numbers[array_key_first($numbers)]-$numbers[array_key_last($numbers)];
                break;
            case (Token::MULTIPLY['value']):
                $result = $numbers[array_key_first($numbers)]*$numbers[array_key_last($numbers)];
                break;
            case (Token::DIVIDE['value']):
                $result=$numbers[array_key_last($numbers)]!==0?$numbers[array_key_first($numbers)]/$numbers[array_key_last($numbers)]:$numbers[array_key_first($numbers)];
                break;
        }
        return $result;
    }
    

    /**
    * Flattens a set expression and removes empty sets.
    *
    * @param array $array The array that represents the set expression. It can contain sets, operators, or parentheses.
    * @return array An array that contains only the non-empty sets in the set expression.
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function flatSetExpression($array){
        if(!Functions::isArray($array)) throw Functions::illegalArguments(__METHOD__);
        $result = [];
        array_walk_recursive($array, function($value) use (&$result) {
            $result[] = $value;   
        });
        return $result;
    }

    /**
    * Checks whether the specified array contains elements whose type is a set
    *
    * @param array $array The array to be checked for non-set elements.
    * @return bool Returns true if non-set elements are found, false otherwise.
    * @throws LibException Thrown if the provided argument is not an array.
    * @public
    */

    public static function isContainsNonSetElement($array) {
        if(!Functions::isArray($array)) throw Functions::illegalArguments(__METHOD__);
        $nonAmpersandStrings = array_filter($array, function($element) {
            return (new Regexp(Token::IDENTIFIER['value']))->test($element)||(gettype($element)==="object"&&get_class($element)===Point::class);
        });
         return !empty($nonAmpersandStrings);
    }

    /**
    * Flattens a set formula and adds an optional operator.
    *
    * @param array $array The array that represents the set formula. It can contain sets or logical operators.
    * @param string|null $op The optional operator to add to the flattened formula. Can be one of the Token constants value of LAND or LOR.
    * @return array An array that contains the flattened set formula with the optional operator.
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function flatSetFormula($array,$op=null){
        if(!Functions::isArray($array)) throw Functions::illegalArguments(__METHOD__);
        $result = [];
        $oparray=[];
        if($op!==null){
            $oparray[]=$op;
        }
        if(array_key_exists('&&',$array)){
            $result=array_diff_key($array,array('&&'=>null));
            $rest=Functions::flatSetFormula($array['&&'],'&&');
            $result=array_merge(array_values($result),array_values($oparray),array_values($rest));
        }
        else if(array_key_exists('||',$array)){
            $result=array_diff_key($array,array('||'=>null));
            $rest=Functions::flatSetFormula($array['||'],'||');
            $result=array_merge(array_values($result),array_values($oparray),array_values($rest));
        }
        else{
            $keys=array_diff(array_keys($array),array('op'));
            if(count($keys)==2){
                $result=[$array[$keys[0]],$op,$array[$keys[1]]];
            }
            else if(count($keys)==1){
                $result=[$array[$keys[0]][0],$op,$array[$keys[0]][1]];
            }
            else if(array_search('(',$array)!==false&&array_search(')',$array)!==false){
                $splittedByKeyType=Functions::splitArrayByKeyType($array);
                $inTheParenthesis=Functions::flatSetFormula($splittedByKeyType['numberKeys'][1]);
                $result=[...$splittedByKeyType['stringKeys'], $op,'(',...$inTheParenthesis,')'];
            }
            
        }
        return $result;
    }

    /**
    * Extracts the first element from each sub-array in an array.
    *
    * @param array $array The array to extract the first element from each sub-array.
    * @return array An array that contains only the first element from each sub-array in the original array.
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function extractArrayFromArray($array){
        $result=[];
        foreach ($array as $value) {
            if(Functions::isArray($value)&&isset($value[0])){
                $result=array_merge(array_values($result),[$value[0]]);
            }
            else {
                $result[]=$value;
            }
        }
        return $result;
    }

    /**
    * Re-appends the variable name to the keys of an array.
    *
    * @param array $array The array to re-append the variable name to its keys.
    * @return array An array that has its keys re-appended with the variable name.
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function reAppendVarnameToArrayKeys($array){
        $retarray=[];
        foreach ($array as $key => $value) {
            if(Functions::isArray($value)){
               $splitted=explode('_', array_key_first($value));
               $varname=implode('_',$splitted);
               $retarray[$key.'_'.$varname]=$value;
            }
            else{
                $retarray[$key.'_'.$value]=$value;
            }
        }
        return $retarray;
    }

    /**
    * Appends the variable name to the keys of an array.
    *
    * @param array $array The array to append the variable name to its keys.
    * @param string $varname The variable name to append to the keys.
    * @return array An array that has its keys appended with the variable name.
    * @throws LibException If the argument is not valid.
    * @public
    */
    public static function appendVarnameToArrayKeys($array,$varname){
        $retarray=[];
        foreach ($array as $key => $value) {
            $retarray[$varname.'_'.$key]=$value;
        }
        return $retarray;
    }


    /**
    * Separates the operands and operations from an array.
    *
    * This function takes an array as an argument and returns an associative array
    * with three keys: 'sets', 'operations', and 'numbers'. The values of these keys
    * are arrays that contain the elements of the original array that belong to each category.
    * The function uses the Functions and Token classes to check the type and value of each element.
    * If the argument is not an array, the function returns the result of calling Functions::illegalArguments
    * with the current method name.
    *
    * @param array $array The array to be separated.
    * @return array An associative array with three keys: 'sets', 'operations', and 'numbers'.
    * @throws LibException If the argument is not an array.
    * @private
    *
    * @codeCoverageIgnore
    */
    private static function separateOperandsAndOperations($array){
        if(!Functions::isArray($array))  throw Functions::illegalArguments(__METHOD__);
        $sets=[];
        $numbers=[];
        $operations=[];
        foreach ($array as $key => $value) {
            if(in_array($value,array(Token::EQUAL['value'],Token::SUBSETOF['value'],
            Token::REALSUBSETOF['value'],Token::SETMINUS['value'],Token::UNION['value'],
            Token::INTERSECTION['value'],Token::COMPLEMENT['value'],Token::PLUS['value'],
            Token::MINUS['value'],Token::MULTIPLY['value'],Token::DIVIDE['value'])))
            {
                $operations[$key]=$value;
            }
            else if(Functions::isSet($value))
            {
                $sets[$key]=$value;
            }
            else if(Functions::isWholeNumber($value))
            {
                $numbers[$key]=$value;
            }
        }
        return ['sets'=>$sets,'operations'=>$operations,'numbers'=>$numbers];
    }

    /**
    * Splits an array by the type of its keys.
    *
    * This function takes an array as an argument and returns an associative array
    * with two keys: 'stringKeys' and 'numberKeys'. The values of these keys
    * are arrays that contain the elements of the original array that have string or numeric keys, respectively.
    * The function uses the Functions class to check the type of each key.
    *
    * @param array $array The array to be split.
    * @return array An associative array with two keys: 'stringKeys' and 'numberKeys'.
    * @private
    *
    * @codeCoverageIgnore
    */
    private static function splitArrayByKeyType($array){
        $keyIsString=[];
        $keyIsNumeric=[];
        foreach ($array as $key => $value) {
            if(Functions::isString($key)){
                $keyIsString[$key]=$value;
            }
            else if(Functions::isNumber($key)){
                $keyIsNumeric[$key]=$value;
            }
        }
        return ["stringKeys"=>$keyIsString,'numberKeys'=>$keyIsNumeric];
    }

    /**
    * Searches for the minimum index among two values.
    *
    * This function takes two values as arguments and returns the minimum index among them.
    * If both values are false, the function returns false. If one of the values is false, the function returns the other value.
    * The function uses the min function to compare the values.
    *
    * @param mixed $first The first value to be compared.
    * @param mixed $second The second value to be compared.
    * @return mixed The minimum index among the two values, or false if both are false.
    * @private
    *
    * @codeCoverageIgnore
    */
    private static function arraySearchMinIndex($first,$second){
        if($first==false&&$second==false) return false;
        if($first===false) return $second;
        if($second===false) return $first;
        return min($first,$second);
    }

    /**
    * Creates a Venn diagram for two or three sets.
    *
    * @param array ...$sets The sets to create the Venn diagram for. There must be either two or three sets as arguments.
    * @return string A base64 encoded PNG image of the Venn diagram.
    * @throws LibException If the arguments are not valid.
    * @public
    */
    public static function Venn($sizeInPt=18,...$sets){
        if(!Functions::isNumber($sizeInPt)) throw Functions::illegalArguments(__METHOD__);
        if(count($sets)>3||count($sets)<=0){
            throw Functions::illegalArguments(__METHOD__);
        }
        foreach ($sets as $set) {
            if(!Functions::isSet($set)) throw Functions::illegalArguments(__METHOD__);
        }
    
        if(count($sets)==1){
            list($seta)=$sets;
            $image=Functions::vennOneSet($seta,$sizeInPt);
        }
        else if(count($sets)==2){
            list($seta,$setb)=$sets;
            $image=Functions::vennTwoSets($seta,$setb,$sizeInPt);
            
        }
        else if(count($sets)==3){
            list($seta,$setb,$setc)=$sets;
            $image=Functions::vennThreeSets($seta,$setb,$setc,$sizeInPt);
        }
        
        ob_start();
        imagepng($image);
        $buffer=ob_get_contents();
        ob_end_clean();
        $data='data:image/png;base64,' . base64_encode($buffer);
        $html='<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
    <img src="'.$data.'"/>
</body>
</html>';
        file_put_contents(Rootfolder::getPhysicalPath().'/images/image.html',$html);
        return getenv('BASEURL').'/images/image.html';
    }

    /**
    * Draws a Venn diagram of a set on an image resource.
    *
    * @param resource &$image The image resource to draw on.
    * @param Set $seta The first set to draw
    * @return \GdImage
    * @private
    *
    * @codeCoverageIgnore
    */
    private static function vennOneSet($seta,$sizeInPt){
        $diagram=new Venn1($seta,'arial_narrow_7.ttf',$sizeInPt);
        $diagram->draw();
        return $diagram->getImage();
    }
    private static function vennTwoSets($seta,$setb,$sizeInPt){
        $diagram=new Venn2($seta,$setb,'arial_narrow_7.ttf',$sizeInPt);
        $diagram->draw();
        return $diagram->getImage();
    }
    private static function vennThreeSets($seta,$setb,$setc,$sizeInPt){
        $diagram=new Venn3($seta,$setb,$setc,'arial_narrow_7.ttf',$sizeInPt);
        $diagram->draw();
        return $diagram->getImage();
    }
   
    /**
    * Initializes the color palette for an image.
    *
    * This method takes an image resource as an argument and assigns it to the static property $colorpalette of the Functions class.
    * The method then uses the imagecolorallocate function to create and store various colors in the $colorpalette array, such as
    * black, white, red, blue, yellow, purple, green, and orange. The method uses the @codeCoverageIgnore annotation to exclude it from
    * code coverage analysis.
    *
    * @param resource $image An image resource, returned by one of the image creation functions, such as imagecreatetruecolor().
    * @public
    *
    * @codeCoverageIgnore
    */
    public static function initializeColorPalette($image){
        Functions::$colorpalette=[];
        Functions::$colorpalette["black"]=imagecolorallocate($image,0,0,0);
        Functions::$colorpalette["white"]=imagecolorallocate($image,255,255,255);
        Functions::$colorpalette["red"]=imagecolorallocate($image,255,0,0);
        Functions::$colorpalette["blue"]=imagecolorallocate($image,0,0,255);
        Functions::$colorpalette["yellow"]=imagecolorallocate($image,255,255,0);
        Functions::$colorpalette["purple"]=imagecolorallocate($image,128,0,128);
        Functions::$colorpalette["green"]=imagecolorallocate($image,0,255,0);
        Functions::$colorpalette["orange"]=imagecolorallocate($image,255,165,0);
    }

    
    /**
    * Gets the color palette for an image.
    *
    * This function returns the static property $colorpalette of the Functions class, which is an associative array that stores various colors for an image resource.
    * The function assumes that the $colorpalette property has been initialized by the initializeColorPalette method, which uses the imagecolorallocate function to create and store the colors.
    * The function uses the @codeCoverageIgnore annotation to exclude it from code coverage analysis.
    *
    * @return array An associative array that stores various colors for an image resource.
    * @public
    *
    * @codeCoverageIgnore
    */
    public static function getColorPalette(){
        return Functions::$colorpalette;
    }

}
