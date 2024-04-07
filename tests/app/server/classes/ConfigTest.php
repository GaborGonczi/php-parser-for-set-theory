<?php
use PHPUnit\Framework\TestCase;
use app\server\classes\Config;

class ConfigTest extends TestCase
{
private $config;
private $host = 'localhost';
private $db = 'test_db';
private $user = 'test_user';
private $password = 'test_password';

protected function setUp(): void
{
$this->config = new Config($this->host, $this->db, $this->user, $this->password);
}

public function testGetHost()
{
$this->assertEquals($this->host, $this->config->getHost());
}

public function testGetDb()
{
$this->assertEquals($this->db, $this->config->getDb());
}

public function testGetUser()
{
$this->assertEquals($this->user, $this->config->getUser());
}

public function testGetPassword()
{
$this->assertEquals($this->password, $this->config->getPassword());
}
}
