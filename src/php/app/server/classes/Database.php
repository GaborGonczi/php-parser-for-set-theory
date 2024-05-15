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
            
            $separatedConditions=$this->separateConditions($where);

            $where_null_conditions=$this->whereNullCond($separatedConditions['nullCond']);

            $where_conditions = $this->whereCond($separatedConditions['filterCond']);

            $sql = "UPDATE " . $table . " SET " . $data_assignments;

            if($where_conditions||$where_null_conditions){
                $sql.=" WHERE ".implode(" AND ", array_diff([$where_conditions,$where_null_conditions], array("")));
            }

            $stmt = $this->dbh->prepare($sql);

            if ($stmt->execute($this->removeQuotationMark($this->removeKeywords(array_merge(array_values($data),array_values($where)))))) {

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
    public function delete($table, $deleted_at, $where) {
        if ($table && is_array($where) && !empty($where)) {

            $where_conditions = $this->whereCond($where);

            $sql = "UPDATE " . $table . " SET deleted_at = ?". " WHERE " . $where_conditions;
      
            $stmt = $this->dbh->prepare($sql);

            if ($stmt->execute(array_values(array_merge(['deleted_at'=>$deleted_at],$where)))) {
        
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
    * @param array|string $table The name of the table.
    * @param array $where The associative array of column names and values to match the records.
    * @return array|bool The array of records as associative arrays on success, or false on failure.
    */
    public function get($table, $where=[],$whereConn=[],$fields='*') {
        $whereConnection ="";
        if(is_array($table)&&!empty($table)){
            $table=implode(',',$table);
        }
        if(!empty($whereConn)){
            $whereConnection=$this->whereConn($whereConn);
        }
        if ($table && is_array($where)) {

            $separatedConditions=$this->separateConditions($where);

            $where_null_conditions=$this->whereNullCond($separatedConditions['nullCond']);

            $where_conditions = $this->whereCond($separatedConditions['filterCond']);

            $in_conditions=$this->inCond($separatedConditions['inCond']);

            $notIn_conditions=$this->notInCond($separatedConditions['notInCond']);



            $sql = "SELECT $fields FROM " . $table;
            if($whereConnection||$where_conditions||$where_null_conditions||$in_conditions||$notIn_conditions){
                $sql.=" WHERE ".implode(" AND ", array_diff([$whereConnection,$where_conditions,$where_null_conditions,$in_conditions,$notIn_conditions], array("")));
            }

            $stmt = $this->dbh->prepare($sql);
       
            if ($stmt->execute($this->removeQuotationMark($this->removeKeywords(array_values($where))))) {
    
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

            $separatedConditions=$this->separateConditions($where);

            $where_null_conditions=$this->whereNullCond($separatedConditions['nullCond']);


            $where_conditions = $this->whereCond($separatedConditions['filterCond']);



            $sql = "SELECT * FROM " . $table;
            if($where_conditions||$where_null_conditions){
                $sql.=" WHERE ".implode(" AND ", array_diff([$where_conditions,$where_null_conditions], array("")));;
            }

            $stmt = $this->dbh->prepare($sql);
           
            if ($stmt->execute($this->removeQuotationMark(array_values($where)))) {
                

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
            $colparts=explode('.',$column);
            if(count($colparts)>1){
                return "`$colparts[0]`.$colparts[1]";
            }
            return "`$column`";
        },$columns);
    }

    private function removeQuotationMark($values){
        return array_map(function ($value) {
            if(gettype($value)==="string"){
                return str_replace('"',' ',$value);
            }
            return $value;
        },$values);
        
    }
    private function whereConn ($conn){
        $kv=[];
        foreach ($conn as $key => $value) {
           $kv[]=$key."=".$value;
        }
        return implode(" AND ",$kv);
    }

    private function separateConditions($where){
        $nullConditions=array_filter($where,function ($value) {
            return ($value===null);
        });
        $inConditions=array_filter($where,function ($value) {
            return str_starts_with((string)$value,'IN');
        });
       
        $notInConditions=array_filter($where,function ($value) {
            return str_starts_with((string)$value,'NOT IN');
        });

        $filterConditions=array_diff_assoc($where,$nullConditions,$inConditions,$notInConditions);

        $notInConditions=array_map(function ($value) {
            return substr($value,0,strlen('NOT IN'));
        },$notInConditions);

        $inConditions=array_map(function ($value) {
            return substr($value,0,strlen('IN'));
        },$inConditions);

        return ['nullCond'=>$nullConditions,'filterCond'=>$filterConditions,'inCond'=>$inConditions,'notInCond'=>$notInConditions];
    }

    private function whereNullCond($where){
       $cond=implode(" <=> ? AND ",$this->backtickColumns(array_keys($where)));
       if($cond) return $cond . " <=> ?";
       return "";
    }

    private function whereCond($where){
        $cond=implode(" = ? AND ", $this->backtickColumns(array_keys($where)));
        if($cond) return $cond . " = ?";
        return "";
    }

    private function inCond($where){
        $cond=implode(" IN ( ? ) AND ", $this->backtickColumns(array_keys($where)));
        if($cond) return $cond . " IN ( ? )";
        return "";
    }

    private function notInCond($where){
        $cond=implode(" NOT IN ( ? ) AND ", $this->backtickColumns(array_keys($where)));
        if($cond) return $cond . " NOT IN ( ? )";
        return "";
    }

    private function removeKeywords($where_values){
        $keywords=['IN','NOT IN','AND'];
        foreach ($keywords as $keyword) {
            $keyword_length = strlen($keyword);
            foreach ($where_values as $key=> $where_value) {       
                $first_part = substr((string)$where_value, 0, $keyword_length);          
                if ($first_part===$keyword) {
                    if($offset=strpos($where_value, "(")){
                        $offset++;
                        $where_values[$key]=substr((string)$where_value,$offset,strlen($where_value)-1);
                        continue;
                    }
                    $where_values[$key]=substr((string)$where_value,$keyword_length);
                    
                }
            }
        }
        return $where_values;
               
    }

    /**
    * Destructor for the Database class. Closes the database connection.
    */
    public function __destruct(){
        $this->dbh=null;
    }
}