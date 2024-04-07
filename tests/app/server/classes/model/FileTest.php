<?php

use app\server\classes\model\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    private $file;

    protected function setUp(): void
    {

        $this->file = new File(5, 1, true, date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), null);
    }

    public function testGetId()
    {
        $this->assertEquals(5, $this->file->getId());
    }

    public function testGetUserId()
    {
        $this->assertEquals(1, $this->file->getUserid());
    }

    public function testSetUserId()
    {
        $this->file->setUserId(2);
        $this->assertEquals(2, $this->file->getUserid());
    }

    public function testGetExample()
    {
        $this->assertTrue($this->file->getExample());
    }
    public function testSetExample()
    {
        $this->file->setExample(false);
        $this->assertFalse($this->file->getExample());
    }

    public function testGetCreatedAt()
    {
        $this->assertEquals(date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), $this->file->getCreatedAt());
    }

    public function testGetModifiedAt()
    {
        $this->assertEquals(date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), $this->file->getModifiedAt());
    }

    public function testSetModifiedAt()
    {
        $newModifiedAt = date('Y-m-d H:i:s', (new DateTime('now'))->add(DateInterval::createFromDateString('1 day'))->getTimestamp());
        $this->file->setModifiedAt($newModifiedAt);
        $this->assertEquals($newModifiedAt, $this->file->getModifiedAt());
    }

    public function testGetDeletedAt()
    {
        $this->assertNull($this->file->getDeletedAt());
    }

    public function testSetDeletedAt()
    {
        $newDeletedAt = date('Y-m-d H:i:s', (new DateTime('now'))->add(DateInterval::createFromDateString('5 days'))->getTimestamp());
        $this->file->setDeletedAt($newDeletedAt);
        $this->assertEquals($newDeletedAt, $this->file->getDeletedAt());
    }
}
