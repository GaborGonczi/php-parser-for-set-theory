<?php
use PHPUnit\Framework\TestCase;
use app\server\classes\Database;
use app\server\classes\Config;
use \PDO;

class DatabaseTest extends TestCase
{
    private $database;
    private $pdo;
    private $config;

    protected function setUp(): void
    {
        
        $this->pdo = $this->createMock(PDO::class);

        
        $this->config = $this->createMock(Config::class);

        
        $this->config->method('getHost')->willReturn('localhost');
        $this->config->method('getDb')->willReturn('test_db');
        $this->config->method('getUser')->willReturn('test_user');
        $this->config->method('getPassword')->willReturn('test_password');

        
        $this->database = new Database($this->config);

        
        $reflection = new \ReflectionClass(Database::class);
        $property = $reflection->getProperty('dbh');
        $property->setAccessible(true);
        $property->setValue($this->database, $this->pdo);
    }

    public function testInsert()
    {
        
        $expectedSql = "INSERT INTO `test_table` (`column1`, `column2`) VALUES (?, ?)";

        
        $stmt = $this->createMock(\PDOStatement::class);

        
        $this->pdo->expects($this->once())->method('prepare')->with($this->equalTo($expectedSql))->willReturn($stmt);

        
        $stmt->expects($this->once())->method('execute')->with($this->equalTo(['value1', 'value2']))->willReturn(true);

        
        $this->pdo->expects($this->once())->method('lastInsertId')->willReturn(1);

        
        $result = $this->database->insert('test_table', ['column1' => 'value1', 'column2' => 'value2']);
        $this->assertEquals(1, $result);
    }

    public function testIsExist()
    {
        
        $expectedSql = "SELECT * FROM test_table WHERE `column1` = ?";

        
        $stmt = $this->createMock(\PDOStatement::class);

        
        $this->pdo->expects($this->once())->method('prepare')->with($this->equalTo($expectedSql))->willReturn($stmt);

        
        $stmt->expects($this->once())->method('execute')->with($this->equalTo(['value1']))->willReturn(true);

        
        $stmt->expects($this->once())->method('rowCount')->willReturn(1);

        
        $result = $this->database->isExist('test_table', ['column1' => 'value1']);
        $this->assertTrue($result);
    }

    
}
