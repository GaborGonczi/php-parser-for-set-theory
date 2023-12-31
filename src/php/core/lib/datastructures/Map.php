<?php
namespace core\lib\datastructures;

use \IteratorAggregate;
use \Traversable;
use \ArrayIterator;
use \JsonSerializable;

/**
* A class that represents a map of key-value pairs.
*
* This class implements the IteratorAggregate and JsonSerializable interfaces to allow iteration and JSON serialization of its elements.
* It also provides methods to add, get, delete, clear, check, and manipulate its elements.
*
* @package core\lib
*/
class Map implements IteratorAggregate, JsonSerializable
{

    /**
    * An associative array that stores the key-value pairs in the map.
    *
    * This property is private and can only be accessed or modified by the methods of the Map class.
    * It is initialized as an empty array in the constructor and can be manipulated by the add, get, delete, clear, and other methods.
    *
    * @var array An associative array that stores the key-value pairs in the map.
    */
    private $elements;

    /**
    * Maps an array of elements to the map using the last unique keys.
    *
    * This function is a private helper function for the constructor.
    * It iterates over the array of elements and assigns them to the map using their keys.
    * If there are duplicate keys, only the last value will be kept.
    *
    * @param array $elements The array of elements to map to the map.
    */
    private function map_with_last_unique_keys($elements)
    {
        foreach ($elements as $key => $value) {
            $this->elements[$key]=$value;
        }
    
    }

    /**
    * Constructs a new map from an array of elements.
    *
    * @param array $elements The array of elements to initialize the map with.
    * The keys will be used as the map keys and the values as the map values.
    * If there are duplicate keys, only the last value will be kept.
    */
    public function __construct($elements)
    {
       $this->elements=[];
       $this->map_with_last_unique_keys($elements);
    }

    /**
    * Adds a key-value pair to the map.
    *
    * This function adds a key-value pair to the map if the key is not null. 
    * It returns the map object itself for method chaining.
    *
    * @param int|string|null $key The key to add to the map. Can be an integer or a string, or null to skip adding.
    * @param mixed $value The value to add to the map.
    * @return Map The map object itself.
    */
    public function add(int|string $key=null, $value)
    {
        if($key!==null){
            $this->elements[$key] = $value;
        }
        return $this;
    }

    /**
    * Gets the value associated with a key in the map.
    *
    * This function returns the value associated with a key in the map if the key exists, or null otherwise.
    *
    * @param int|string $key The key to get the value from.
    * @return mixed|null The value associated with the key, or null if the key does not exist.
    */
    public function get(int|string $key)
    {
        return $this->has($key) ? $this->elements[$key] : null;

    }

    /**
    * Clears the map of all elements.
    *
    * This function sets the map to an empty array and returns null.
    *
    * @return null
    */
    public function clear()
    {
        $this->elements = [];
        return null;
    }

    /**
    * Deletes a key-value pair from the map.
    *
    * This function deletes a key-value pair from the map if the key is not null. It does not return anything.
    *
    * @param int|string|null $key The key to delete from the map. Can be an integer or a string, or null to skip deleting.
    */
    public function delete(int|string $key=null)
    {
        if($key!==null){
            unset($this->elements[$key]);
        }
        
    }

    /**
    * Checks if the map has a key.
    *
    * This function returns true if the map has a key, false otherwise.
    *
    * @param int|string $key The key to check in the map.
    * @return bool True if the map has the key, false otherwise.
    */    
    public function has(int|string $key)
    {
        return array_key_exists($key, $this->elements);
    }

    /**
    * Gets the size of the map.
    *
    * This function returns the number of elements in the map.
    *
    * @return int The size of the map.
    */
    public function size()
    {
        return count($this->elements);
    }

    /**
    * Gets the keys of the map.
    *
    * This function returns an array of the keys of the map.
    *
    * @return array An array of the keys of the map.
    */
    public function keys()
    {
        return array_keys($this->elements);
    }

    /**
    * Gets the values of the map.
    *
    * This function returns an array of the values of the map.
    *
    * @return array An array of the values of the map.
    */
    public function values()
    {
        return array_values($this->elements);
    }

    /**
    * Gets the entries of the map.
    *
    * This function returns an associative array of the key-value pairs in the map.
    *
    * @return array An associative array of the key-value pairs in the map.
    */
    public function entries()
    {
        return $this->elements;
    }

    /**
    * Gets an iterator for the map.
    *
    * This function implements the IteratorAggregate interface and returns an ArrayIterator for the map elements.
    * This allows iterating over the map using foreach loops or other methods that accept Traversable objects.
    *
    * @return Traversable An ArrayIterator for the map elements.
    */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }

    /**
    * Compares two maps for equality.
    *
    * This function compares two maps for equality by checking if they have the same entries.
    * It returns true if they are equal, false otherwise.
    *
    * @param Map $map The other map to compare with this one.
    * @return bool True if the maps are equal, false otherwise.
    */
    public function areEqual(Map $map):bool
    {
        return empty(array_diff($this->entries(),$map->entries()))&&empty(array_diff($map->entries(),$this->entries()));
    }

    /**
    * Converts the map to a string representation.
    *
    * This function implements the magic method __toString and returns a string representation of the map.
    * The string format is "{key:value,key:value,...}" where key and value are replaced by their actual values.
    * If there are no elements in the map, it returns "{}".
    *
    * @return string A string representation of the map.
    */
    public function __toString()
    {
        $keys = $this->keys();
        return "{".implode(",",array_map(function  ($elem)  {
            return $elem.":".$this->elements[$elem];
        },$keys))."}";
    }

    /**
    * Serializes the map to JSON format.
    *
    * This function implements the JsonSerializable interface and returns an array of key-value pairs in JSON format.
    * This allows encoding the map using json_encode or other methods that accept JsonSerializable objects.
    *
    * @return mixed An array of key-value pairs in JSON format.
    */
    public function jsonSerialize() :mixed {
        $json=[];
        foreach ($this->elements as $key => $value) {
           $json[]=[$key=>$value];
        }
        return $json;
    }
}