<?php
namespace app\server\classes\model;

class Expression extends Model{

    private $id;
    private $file_id;
    private $statement;
    private $result;
    private $start;
    private $end;
    private $noparse;
    private $length; 
    private $created_at;
    private $modified_at;
    private $deleted_at;
    
    public function __construct($id, $file_id, $statement, $result, $start, $end, $noparse, $created_at, $modified_at,$deleted_at) {
   
        $this->id = $id; 
        $this->file_id = $file_id; 
        $this->statement = $statement; 
        $this->result = $result; 
        $this->start = $start; 
        $this->end = $end; 
        $this->length=$this->end-$this->start;
        $this->noparse = $noparse; 
        $this->created_at = $created_at; 
        $this->modified_at = $modified_at; 
        $this->deleted_at = $deleted_at; 

    }
    
    // Getters and setters
    
    public function getId() { 
        return $this->id; 
    }
    
    public function getFileId() { 
        return $this->file_id; 
    }
    
    public function setFileId($file_id) { 
        $this->file_id = $file_id;
    }
    
    public function getStatement() { 
        return $this->statement; 
    }
    
    public function setStatement($statement) { 
        $this->statement = $statement; 
    }
    
    public function getResult() { 
        return $this->result; 
    }
    
    public function setResult($result) { 
        $this->result = $result; 
    }
    
    public function getStart() { 
        return $this->start; 
    }
    
    public function setStart($start) { 
        $this->start = $start; 
    }
    
    public function getEnd() { 
        return $this->end; 
    }
    
    public function setEnd($end) { 
        $this->end = $end; 
    }
    
    public function getNoparse() { 
        return $this->noparse; 
    }
    
    public function setNoparse($noparse) { 
        $this->noparse = $noparse; 
    }
    
    public function getLength() { 
        return $this->length;
    }
    
    public function getCreatedAt() { 
        return $this->created_at; 
    }
    
    public function getModifiedAt() {
        return $this->modified_at;
    }

    public function setModifiedAt($modified_at) {
        $this->modified_at = $modified_at;
    }

    public function getDeletedAt() {
        return $this->deleted_at;
    }

    public function setDeletedAt($deleted_at) {
        $this->deleted_at = $deleted_at;
    }

}
    