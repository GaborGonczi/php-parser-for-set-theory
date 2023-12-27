<?php
namespace app\server\classes\model;

use \ReflectionClass;

/**
* An abstract class that represents a model for the application.
* A model is a class that encapsulates the data and logic of a domain entity.
* A model can be serialized into an associative array for easy storage and retrieval.
*
* @package app\server\classes\model
*/
abstract class Model {

    /**
    * A method that returns the model as an associative array.
    * The keys of the array are the names of the properties of the model, and the values are their values.
    * @return array An associative array that represents the model.
    */
    public function getAsAssociativeArray():array {
        $ref = new ReflectionClass($this);
        $props = $ref->getProperties();
        $result = array();
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $name = $prop->getName();
            $value = $prop->getValue($this);
            $result[$name] = $value;
        }
        return $result;

    }

    /**
    * A magic method that serializes the model into an array.
    * This method is invoked when calling serialize() on a model object.
    * @return array An array that contains the serialized data of the model.
    */
    public function __serialize(): array {
        return $this->getAsAssociativeArray();
    }
}
