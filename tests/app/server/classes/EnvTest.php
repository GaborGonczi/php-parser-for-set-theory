<?php
use PHPUnit\Framework\TestCase;
use app\server\classes\Env;

class EnvTest extends TestCase
{
    private $env;
    private $path;

    protected function setUp(): void
    {
        $this->path = realpath(__DIR__).'/.env';
        file_put_contents($this->path, "VAR1=value1\nVAR2=value2");
        $this->env = new Env($this->path);
    }

    protected function tearDown(): void
    {
        unlink($this->path);
    }

    public function testLoad()
    {
        $this->env->load();
        $this->assertEquals('value1', getenv('VAR1'));
        $this->assertEquals('value2', $_ENV['VAR2']);
        $this->assertEquals('value2', $_SERVER['VAR2']);
    }

    public function testConstructorException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Env('nonexistent.env');
    }

    public function testLoadException()
    {
        // Make the file unreadable
        chmod($this->path, 0000);

        $this->expectException(\RuntimeException::class);
        $this->env->load();

        // Restore file permissions
        chmod($this->path, 0644);
    }

    // Add more tests to cover different scenarios and edge cases
}