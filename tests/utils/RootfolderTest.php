<?php

use PHPUnit\Framework\TestCase;
use \utils\Rootfolder;

class RootfolderTest extends TestCase
{

    protected function setUp():void {
        $_SERVER['SERVER_PORT'] = 443;
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/src/index.php';
        $_SERVER['DOCUMENT_ROOT'] = '/var/www/html';
    }

    public function testGetPath()
    {
        $this->assertEquals('https://example.com', Rootfolder::getPath());
    }

    public function testGetPhysicalPath()
    {
        $this->assertEquals('/var/www/html', Rootfolder::getPhysicalPath());
    }
}