<?php
namespace app\server\classes;

use \PDO;
use \PDOException;

/**
* Database class represents a wrapper for interacting with a MySQL database.
* 
* This class uses PDO to establish a connection to the database and perform CRUD operations. 
*It also has methods to check the existence of a record and backtick the column names.
* 
* @package app\server\classes
*/
class Database {

    /**
    * The PDO object that represents the database connection.
    * 
    * @var PDO
    */
    private $dbh;

    /**
    * The Config object that contains the database configuration.
    * 
    * @var Config
    */
    private $config;

    /**
    * Constructor for the Database class.
    * 
    * This method creates a PDO object and establishes a connection to the database using the Config object.
    * It also sets the error mode attribute to throw exceptions.
    * If the connection fails, it prints the error message.
    * 
    * @param Config $config The Config object that contains the database configuration.
    */
    public function __construct(Config $config) {
        $this->config = $config;

        try {
                $this->dbh = new PDO("mysql:host=" . $this->config->getHost() . ";dbname=" . $this->config->getDb(),
                $this->config->getUser(),$this->config->getPassword());
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage() . "\n";
            }
    }


    /**
    * Insert a record into a table.
    * 
    * @param string $table The name of the table.
    * @param array $data The associative array of column names and values to insert.
    * @return int|bool The last insert id on success, or false on failure.
    */
    public function insert($table, $data) {
        if ($table && is_array($data) && !empty($data)) {
            $keys=array_keys($data);

            $sql = "INSERT INTO " . $table . " (" . implode(", ",$this->backtickColumns($keys)) . ") VALUES (" .implode(',', array_fill(0, count($keys), '?')).  ")";

            $stmt = $this->dbh->prepare($sql);

            if ($stmt->execute(array_values($data))) {
                return $this->dbh->lastInsertId();
            } 
            else {
                return false;
            }
        } 
        else {
            return false;
        }
    }

    /**
    * Update a record in a table.
    * 
    * @param string $table The name of the table.
    * @param array $data The associative array of column names and values to update.
    * @param array $where The associative array of column names and values to match the record.
    * @return int|bool The number of affected rows on success, or false on failure.
    */
    public function update($table, $data, $where) {

        if ($table && is_array($data) && !empty($data) && is_array($where) && !empty($where)) {

            $data_assignments = implode(" = ?, ", $this->backtickColumns(array_keys($data))) . " = ?";

            $where_conditions = implode(" = ? AND ", $this->backtickColumns(array_keys($where))) . " = ?";

            $sql = "UPDATE " . $table . " SET " . $data_assignments . " WHERE " . $where_conditions;

            $stmt = $this->dbh->prepare($sql);

            if ($stmt->execute(array_merge(array_values($data), array_values($where)))) {

                return $stmt->rowCount();
            } 
            else {
        
                return false;
            }
        } 
        else {
            return false;
        }
    }

    /**
    * Delete a record from a table.
    * 
    * @param string $table The name of the table.
    * @param array $where The associative array of column names and values to match the record.
    * @return int|bool The number of affected rows on success, or false on failure.
    */
    public function delete($table, $where) {
        if ($table && is_array($where) && !empty($where)) {

            $where_conditions = implode(" = ? AND ", $this->backtickColumns(array_keys($where))) . " = ?";

            $sql = "DELETE FROM " . $table . " WHERE " .$where_conditions;

            $stmt = $this->dbh->prepare($sql);

            if ($stmt->execute(array_values($where))) {
                return $stmt->rowCount();
            } 
            else {
                return false;
            }
        } 
        else {
            return false;
        }
    }

    /**
    * Get one or more records from a table.
    * 
    * @param string $table The name of the table.
    * @param array $where The associative array of column names and values to match the records.
    * @return array|bool The array of records as associative arrays on success, or false on failure.
    */
    public function get($table, $where) {
        if ($table && is_array($where) && !empty($where)) {
        
            $where_conditions = implode(" = ? AND ", $this->backtickColumns(array_keys($where))) . " = ?";
            
            $sql = "SELECT * FROM " . $table . " WHERE " . $where_conditions;
            
            $stmt = $this->dbh->prepare($sql);
        
            if ($stmt->execute(array_values($where))) {
        
                if ($stmt->rowCount() > 0) {
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
    * Check if a record exists in a table.
    * 
    * @param string $table The name of the table.
    * @param array $where The associative array of column names and values to match the record.
    * @return bool True if the record exists, false otherwise.
    */
    public function isExist($table, $where) {
        if ($table && is_array($where) && !empty($where)) {

            $where_conditions = implode(" = ? AND ", $this->backtickColumns(array_keys($where))) . " = ?";

            $sql = "SELECT * FROM " . $table . " WHERE " . $where_conditions;

            $stmt = $this->dbh->prepare($sql);

            if ($stmt->execute(array_values($where))) {

                if ($stmt->rowCount() > 0) {
                    return true;
                } 
                else {
                    return false;
                }
            } 
            else {
                return false;
            }
        } 
        else {
            return false;
        }
    }

    /**
    * Add backticks to column names for SQL queries.
    * 
    * @param array $columns The array of column names.
    * @return array The array of column names with backticks added.
    */
    private function backtickColumns($columns){
        return array_map(function($column){
            return "`$column`";
        },$columns);
    }

    /**
    * Destructor for the Database class. Closes the database connection.
    */
    public function __destruct(){
        $this->dbh=null;
    }
}