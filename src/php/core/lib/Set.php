<?php
namespace core\lib;
use \IteratorAggregate;
use \Traversable;
use \ArrayIterator;
class Set implements IteratorAggregate{

    private $elements;
    public function __construct($elements){
        $this->elements=array_unique($elements);
    }
    public function add($element){
        $this->elements=array_unique(array_merge($this->elements,[$element]));
        return $this;
    }
    public function clear(){
        $this->elements=[];
        return null;
    }
    public function delete($element){
        $this->elements=array_diff($this->elements,[$element]);
        return $this;
    }
    public function has($element){
        return in_array($element,$this->elements);
    }
    public function size() {
        return count($this->elements);
    }
    public function getIterator() :Traversable{
        return new ArrayIterator($this->elements);
    }
    public function values(){
        return $this->elements;
    }
    public function __toString()  {
        return '{'.implode(',',$this->elements).'}';
    }
}