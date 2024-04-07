<?php

use app\server\classes\model\Automaton;
use PHPUnit\Framework\TestCase;

class QuestionaireTest extends TestCase
{
private $automaton;

protected function setUp(): void
{
// Create a new Automaton object before each test
$this->automaton = new Automaton(1, 10, '/path/to/automaton', '2024-03-12 17:44:17', '2024-03-12 18:44:17', null);
}

public function testGetId()
{
$this->assertEquals(1, $this->automaton->getId());
}

public function testGetExpressionId()
{
$this->assertEquals(10, $this->automaton->getExpressionId());
}

public function testSetExpressionId()
{
$this->automaton->setExpressionId(20);
$this->assertEquals(20, $this->automaton->getExpressionId());
}

public function testGetPath()
{
$this->assertEquals('/path/to/automaton', $this->automaton->getPath());
}

public function testSetPath()
{
$newPath = '/new/path/to/automaton';
$this->automaton->setPath($newPath);
$this->assertEquals($newPath, $this->automaton->getPath());
}

public function testGetCreatedAt()
{
$this->assertEquals('2024-03-12 17:44:17', $this->automaton->getCreatedAt());
}

public function testGetModifiedAt()
{
$this->assertEquals('2024-03-12 18:44:17', $this->automaton->getModifiedAt());
}

public function testSetModifiedAt()
{
$newModifiedAt = '2024-03-12 19:44:17';
$this->automaton->setModifiedAt($newModifiedAt);
$this->assertEquals($newModifiedAt, $this->automaton->getModifiedAt());
}

public function testGetDeletedAt()
{
$this->assertNull($this->automaton->getDeletedAt());
}

public function testSetDeletedAt()
{
$newDeletedAt = '2024-03-12 20:44:17';
$this->automaton->setDeletedAt($newDeletedAt);
$this->assertEquals($newDeletedAt, $this->automaton->getDeletedAt());
}
}
