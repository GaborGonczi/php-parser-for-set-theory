<?php

class Database {

    private $dbh;
    private $config;


    public function __construct(Config $config) {
        $this->config = $config;

        try {
                $this->dbh = new PDO("mysql:host=" . $this->config->getHost() . ";dbname=" . $this->config->getDb(),
                $this->config->getUser(),$this->config->getPassword());
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo "Connected successfully to the database.\n";
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage() . "\n";
            }
    }


    public function insert($table, $data) {
        if ($table && is_array($data) && !empty($data)) {
            $keys=array_keys($data);

            $sql = "INSERT INTO " . $table . " (" . implode(", ",$keys ) . ") VALUES (" .implode(',', array_fill(0, count($keys), '?')).  ")";

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
    public function update($table, $data, $where) {

        if ($table && is_array($data) && !empty($data) && is_array($where) && !empty($where)) {

            $data_assignments = implode(" = ?, ", array_keys($data)) . " = ?";

            $where_conditions = implode(" = ? AND ", array_keys($where)) . " = ?";

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
    public function delete($table, $where) {
        if ($table && is_array($where) && !empty($where)) {

            $where_conditions = implode(" = ? AND ", array_keys($where)) . " = ?";

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
    public function isExist($table, $data) {
        if ($table && is_array($data) && !empty($data)) {

            $data_conditions = implode(" = ? AND ", array_keys($data)) . " = ?";

            $sql = "SELECT * FROM " . $table . " WHERE " . $data_conditions;

            $stmt = $this->dbh->prepare($sql);

            if ($stmt->execute(array_values($data))) {

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

}