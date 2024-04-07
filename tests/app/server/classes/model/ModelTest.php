<?php

use PHPUnit\Framework\TestCase;
use app\server\classes\model\Model;

// Create a concrete subclass of Model for testing
class ConcreteModel extends Model
{
    private $property1;
    private $property2;

    public function __construct($property1, $property2)
    {
        $this->property1 = $property1;
        $this->property2 = $property2;
    }
}

class ModelTest extends TestCase
{
    public function testGetAsAssociativeArray()
    {
        $model = new ConcreteModel('value1', 'value2');
        $expectedArray = [
            'property1' => 'value1',
            'property2' => 'value2'
        ];
        
        $this->assertIsArray($model->getAsAssociativeArray());

        $this->assertEquals($expectedArray, $model->getAsAssociativeArray());
    }

    public function testJsonSerialize()
    {
        $model = new ConcreteModel('value1', 'value2');
        $expectedArray = [
            'property1' => 'value1',
            'property2' => 'value2'
        ];
        $this->assertSame(json_encode($expectedArray), json_encode($model));
    }
}