<?php

use app\server\classes\model\Automaton;
use PHPUnit\Framework\TestCase;

class AutomatonTest extends TestCase
{
    private $automaton;

    protected function setUp(): void
    {

        $this->automaton = new Automaton(1, 10, '/path/to/automaton', date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), null);
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
        $this->assertEquals(date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), $this->automaton->getCreatedAt());
    }

    public function testGetModifiedAt()
    {
        $this->assertEquals(date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), $this->automaton->getModifiedAt());
    }

    public function testSetModifiedAt()
    {
        $newModifiedAt = date('Y-m-d H:i:s', (new DateTime('now'))->add(DateInterval::createFromDateString('1 day'))->getTimestamp());
        $this->automaton->setModifiedAt($newModifiedAt);
        $this->assertEquals($newModifiedAt, $this->automaton->getModifiedAt());
    }

    public function testGetDeletedAt()
    {
        $this->assertNull($this->automaton->getDeletedAt());
    }

    public function testSetDeletedAt()
    {
        $newDeletedAt = date('Y-m-d H:i:s', (new DateTime('now'))->add(DateInterval::createFromDateString('5 days'))->getTimestamp());
        $this->automaton->setDeletedAt($newDeletedAt);
        $this->assertEquals($newDeletedAt, $this->automaton->getDeletedAt());
    }
}
