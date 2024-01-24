<?php
namespace core\lib\datastructures;

use \IteratorAggregate;
use \Traversable;
use \ArrayIterator;
use \JsonSerializable;
use \ReflectionClass;

/**
* A class that represents a set of unique elements.
*
* @package core\lib
*/
class Set implements IteratorAggregate,JsonSerializable {

    /**
    * A private property that stores the elements of the set as an array.
    *
    * This property is private and can only be accessed or modified by the methods of the Set class.
    * It is initialized with  array of elements in the constructor and can be manipulated by the add, get, delete, clear, and other methods.
    *
    * @var array $elements The array of unique elements in the set.
    */
    private $elements;

        
    /**
    * The type of the set.
    *
    * This property stores the type of the set, which can be one of the following values: 'integer', 'double', 'boolean', or 'string' or a user-defined type.
    * The type of the set determines how the elements of the set are compared and sorted. The type of the set is null by default,
    * until it is initialized by the constructor or the setType method of the Set class.
    *
    * @var string|null The type of the set.
    */
    private $type=null;

    /**
    * Constructs a new Set object from an array of elements.
    *
    * This method takes an array of elements as an argument and assigns it to the $elements property of the Set class, after filtering out the non-unique and non-matching elements.
    * The method also sets the $type property of the Set class, based on the type or class of the first element in the array, if any.
    * The method uses the @param tag to indicate the type and name of the argument, which is an array.
    *
    * @param array $elements The array of elements to initialize the set with. Only the unique and matching elements will be kept.
    */
    public function __construct($elements)
    {
        if(!empty($elements)){
            $elements=array_values($elements);
            $this->type=gettype($elements[0])!=='object'?gettype($elements[0]):get_class($elements[0]);
            $elements=array_filter($elements,function ($element) {
                if(gettype($element)!=='object'){
                    return $this->type===gettype($element);
                }
                else {
                    return $this->type===get_class($element);
                }
                
            });
        }
        
        $this->elements=array_unique($elements);
    }

    /**
    * Adds an element to the set if it is not already present.
    * Also sets the $type property of the Set class, if it is null, based on the type or class of the element.
    *
    * @param mixed $element The element to add to the set.
    * @return Set The same Set object, for method chaining.
    */
    public function add($element)
    {
        if($this->type===null){
            $this->type=gettype($element)!=='object'?gettype($element):get_class($element);
        }
        $this->elements=array_unique(array_merge($this->elements,[$element]));
        return $this;
    }

    /**
    * Clears the set of all elements and sets the $type property of the Set class to null.
    *
    * @return null
    */
    public function clear()
    {
        $this->elements=[];
        $this->type=null;
        return null;
    }

    /**
    * Deletes an element from the set if it is present.
    * Also sets the $type property of the Set class to null, if the $elements property becomes empty after the deletion.
    *
    * @param mixed $element The element to delete from the set.
    * @return Set The same Set object, for method chaining.
    */
    public function delete($element)
    {
        $this->elements=array_diff($this->elements,[$element]);
        if($this->size()===0){
            $this->type=null;
        }

        return $this;
    }

    /**
    * Checks if an element is present in the set.
    *
    * @param mixed $element The element to check for in the set.
    * @return bool True if the element is present in the set, false otherwise.
    */
    public function has($element)
    {
        return in_array($element,$this->elements);
    }

    /**
    * Gets the size of the set, i.e. the number of elements in the set.
    *
    * @return int The size of the set.
    */
    public function size() 
    {
        return count($this->elements);
    }

    /**
    * Gets an iterator for the set, which can be used to traverse the elements of the set in a loop.
    *
    * @return Traversable An iterator object for the set, which implements the Traversable interface.
    */
    public function getIterator() :Traversable
    {
        return new ArrayIterator($this->elements);
    }

    /**
    * Gets the values of the set as an array.
    *
    * @return array An array of the elements of the set.
    */
    public function values()
    {
        return $this->elements;
    }

    /**
    * Checks if two sets are equal by comparing their elements.
    *
    * @param Set $set The other set to compare with.
    * @return bool True if the sets have the same elements, false otherwise.
    */
    public function areEqual(Set $set)
    {
        return empty(array_diff($this->values(),$set->values()))&&empty(array_diff($set->values(),$this->values()));

    }

    /**
    * Sorts the elements of the set in ascending order.
    *
    * This method checks the type of the set, which is a property of the set class, and uses the sort function to sort the elements of the set, which is another property of the set class, in ascending order.
    * If the type of the set is not 'integer', 'double', or 'boolean', the method uses the SORT_STRING flag to sort the elements as strings.
    * Otherwise, the method uses the default sort order. The method returns the set object itself for method chaining.
    *
    * @return Set The set object with sorted elements.
    */
    public function orderByAsc(){
        if(!in_array($this->type,array('integer','double','boolean'))){
            sort($this->elements,SORT_STRING);
        }
        else{
            sort($this->elements);
        }
        
        return $this;
    }

    /**
    * Returns a string representation of the set in the format "{element1,element2,...}".
    *
    * @return string The string representation of the set.
    */
    public function __toString()
    {
        return '{'.implode(',',$this->elements).'}';
    }

    /**
    * Gets the name of the Set class.
    *
    * This method uses the ReflectionClass class to get the short name of the ASet class, which is the class name without the namespace.
    * The method returns the short name as a string.
    *
    * @return string The short name of the ASet class.
    */
    private function getName()
    {
        $ref=new ReflectionClass($this);
        return $ref->getShortName();

    }

    /**
    * Serializes the Set object to JSON format.
    *
    * This method implements the JsonSerializable interface, which allows the ASet object to be serialized by the json_encode function.
    * The method returns an associative array with two keys: 'name' and 'elements'.
    * The values of these keys are the name and elements of the Set object, respectively.
    *
    * @return array An associative array with two keys: 'name' and 'elements'.
    */
    public function jsonSerialize():mixed
    {
        return ['name'=>$this->getName(),'elements'=>$this->elements,"type"=>$this->type];
    }
}