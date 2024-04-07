<?php

use app\server\classes\model\Expression;
use PHPUnit\Framework\TestCase;

class ExpressionTest extends TestCase
{
    private $expression;

    protected function setUp(): void
    {
        $this->expression = new Expression(1, 10, 'A:={1,2,3}','{1,2,3}',0,10,false,0, date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), null);
    }

    public function testGetId()
    {
        $this->assertEquals(1, $this->expression->getId());
    }

    public function testGetFileId()
    {
        $this->assertEquals(10, $this->expression->getFileId());
    }

    public function testSetFileId()
    {
        $this->expression->setFileId(25);
        $this->assertEquals(25, $this->expression->getFileId());
    }

    public function testGetStatement()
    {
        $this->assertEquals('A:={1,2,3}', $this->expression->getStatement());
    }

    public function testSetStatement()
    {
        $this->expression->setStatement('A:={1,2,3,4}');
        $this->assertEquals('A:={1,2,3,4}', $this->expression->getStatement());
    }

    public function testGetResult()
    {
        $this->assertEquals('{1,2,3}', $this->expression->getResult());
    }

    public function testSetResult()
    {
        $this->expression->setResult('{1,2,3,4}');
        $this->assertEquals('{1,2,3,4}', $this->expression->getResult());
    }

    public function testGetStart()
    {
        $this->assertEquals(0, $this->expression->getStart());
    }

    public function testSetStart()
    {
        $this->expression->setStart(1);
        $this->assertEquals(1, $this->expression->getStart());
    }

    public function testGetEnd()
    {
        $this->assertEquals(10, $this->expression->getEnd());
    }

    public function testSetEnd()
    {
        $this->expression->setEnd(12);
        $this->assertEquals(12, $this->expression->getEnd());
    }

    public function testGetNoparse()
    {
        $this->assertFalse($this->expression->getNoparse());
    }

    public function testSetNoparse()
    {
        $this->expression->setNoparse(true);
        $this->assertTrue($this->expression->getNoparse());
    }

    public function testGetRow()
    {
        $this->assertEquals(0,$this->expression->getRow());
    }

    public function testSetRow()
    {
        $this->expression->setRow(1);
        $this->assertEquals(1,$this->expression->getRow());
    }

    public function testGetCreatedAt()
    {
        $this->assertEquals(date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), $this->expression->getCreatedAt());
    }

    public function testGetModifiedAt()
    {
        $this->assertEquals(date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), $this->expression->getModifiedAt());
    }

    public function testSetModifiedAt()
    {
        $newModifiedAt = date('Y-m-d H:i:s', (new DateTime('now'))->add(DateInterval::createFromDateString('1 day'))->getTimestamp());
        $this->expression->setModifiedAt($newModifiedAt);
        $this->assertEquals($newModifiedAt, $this->expression->getModifiedAt());
    }

    public function testGetDeletedAt()
    {
        $this->assertNull($this->expression->getDeletedAt());
    }

    public function testSetDeletedAt()
    {
        $newDeletedAt = date('Y-m-d H:i:s', (new DateTime('now'))->add(DateInterval::createFromDateString('5 days'))->getTimestamp());
        $this->expression->setDeletedAt($newDeletedAt);
        $this->assertEquals($newDeletedAt, $this->expression->getDeletedAt());
    }
}
