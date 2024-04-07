<?php

use app\server\classes\model\Variable;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
private $variable;

protected function setUp(): void
{
// Create a new Automaton object before each test
$this->variable = new Variable(1, 10, 'A','{1,2,3}', date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), null);
}

public function testGetId()
{
$this->assertEquals(1, $this->variable->getId());
}

public function testGetFileId()
{
$this->assertEquals(10, $this->variable->getFileId());
}

public function testSetFileId()
{
$this->variable->setFileId(11);
$this->assertEquals(11, $this->variable->getFileId());
}

public function testGetName()
{
$this->assertEquals('A', $this->variable->getName());
}

public function testSetName()
{

$this->variable->setName('B');
$this->assertEquals('B', $this->variable->getName());
}

public function testGetValue()
{
$this->assertEquals('{1,2,3}', $this->variable->getValue());
}

public function testSetValue()
{

$this->variable->setValue('{1,2,3,4}');
$this->assertEquals('{1,2,3,4}', $this->variable->getValue());
}


public function testGetCreatedAt()
{
    $this->assertEquals(date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), $this->variable->getCreatedAt());
}

public function testGetModifiedAt()
{
    $this->assertEquals(date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), $this->variable->getModifiedAt());
}

public function testSetModifiedAt()
{
    $newModifiedAt = date('Y-m-d H:i:s', (new DateTime('now'))->add(DateInterval::createFromDateString('1 day'))->getTimestamp());
    $this->variable->setModifiedAt($newModifiedAt);
    $this->assertEquals($newModifiedAt, $this->variable->getModifiedAt());
}

public function testGetDeletedAt()
{
    $this->assertNull($this->variable->getDeletedAt());
}

public function testSetDeletedAt()
{
    $newDeletedAt = date('Y-m-d H:i:s', (new DateTime('now'))->add(DateInterval::createFromDateString('5 days'))->getTimestamp());
    $this->variable->setDeletedAt($newDeletedAt);
    $this->assertEquals($newDeletedAt, $this->variable->getDeletedAt());
}
}
