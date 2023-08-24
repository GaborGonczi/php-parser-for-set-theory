<?php
namespace app\server\classes\model;

use \ReflectionClass;

abstract class Model {
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

    public function __serialize(): array {
        return $this->getAsAssociativeArray();
    }
}
