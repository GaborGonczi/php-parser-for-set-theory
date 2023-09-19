<?php
namespace core\lib;

use \IteratorAggregate;
use \Traversable;
use \ArrayIterator;
use \JsonSerializable;

class Map implements IteratorAggregate, JsonSerializable
{

    private $elements;
    private function map_with_last_unique_keys($elements){
        $arr=array_map(function ($element) {
            return $element.uniqid();
        },$elements);
        $arr=array_flip(array_flip($arr));
        return  array_map(function ($element) {
            return substr($element,0,strlen($element)-strlen(uniqid()));
        },$arr);
    
    }
    public function __construct($elements){
        $this->elements=$this->map_with_last_unique_keys($elements);
    }
    public function add($key, $value)
    {
        $this->elements[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        return $this->has($key) ? $this->elements[$key] : null;

    }
    public function clear()
    {
        $this->elements = [];
        return null;
    }

    public function delete($key)
    {
        unset($this->elements[$key]);
    }

    public function has($key)
    {
        return array_key_exists($key, $this->elements);
    }
    public function size()
    {
        return count($this->elements);
    }

    public function keys()
    {
        return array_keys($this->elements);
    }

    public function values()
    {
        return array_values($this->elements);
    }
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }

    public function __toString()
    {
        $keys = $this->keys();
        return "{".implode(",",array_map(function  ($elem)  {
            return $elem.":".$this->elements[$elem];
        },$keys))."}";

       

    }
    public function jsonSerialize() :mixed {
        $json=[];
        foreach ($this->elements as $key => $value) {
           $json[]=[$key=>$value];
        }
        return $json;
    }
}