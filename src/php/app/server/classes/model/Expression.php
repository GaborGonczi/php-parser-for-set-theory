<?php
namespace app\server\classes\model;


/**
* Expression class represents a mathematical expression in a file.
* 
* This class extends the Model class and has properties for the
* expression id, file id, statement, result, start and end positions, row, length, noparse flag, and timestamps.
* 
* @package app\server\classes\model
*/
class Expression extends Model{

   /**
    * The expression id.
    * 
    * @var int
    */
    private $id;

    /**
    * The file id that contains the expression.
    * 
    * @var int
    */
    private $file_id;

    /**
    * The expression statement.
    * 
    * @var string
    */
    private $statement;

    /**
    * The expression result.
    * 
    * @var string
    */
    private $result;

    /**
    * The start position of the expression in the file.
    * 
    * @var int
    */
    private $start;

    /**
    * The end position of the expression in the file.
    * 
    * @var int
    */
    private $end;

    /**
    * The flag to indicate if the expression should be parsed or not.
    * 
    * @var bool
    */
    private $noparse;

    /**
    * The length of the expression in the file.
    * 
    * @var int
    */
    private $length;

    /**
    * The row of the expression in the file.
    * 
    * @var int
    */
    private $row;

    /**
    * The timestamp of when the expression was created.
    * 
    * @var string
    */
    private $created_at;

    /**
    * The timestamp of when the expression was modified.
    * 
    * @var string
    */
    private $modified_at;

    /**
    * The timestamp of when the expression was deleted.
    * 
    * @var string
    */
    private $deleted_at;
    
    /**
    * Constructor for the Expression class.
    * 
    * @param int|null $id The expression id.
    * @param int $file_id The file id that contains the expression.
    * @param string $statement The expression statement.
    * @param string $result The expression result.
    * @param int $start The start position of the expression in the file.
    * @param int $end The end position of the expression in the file.
    * @param bool $noparse The flag to indicate if the expression should be parsed or not.
    * @param int $row The row of the expression in the file.
    * @param string $created_at The timestamp of when the expression was created.
    * @param string $modified_at The timestamp of when the expression was modified.
    * @param string $deleted_at The timestamp of when the expression was deleted.
    */
    public function __construct($id, $file_id, $statement, $result, $start, $end, $noparse, $row, $created_at, $modified_at,$deleted_at) {
   
        $this->id = $id; 
        $this->file_id = $file_id; 
        $this->statement = $statement; 
        $this->result = $result; 
        $this->start = $start; 
        $this->end = $end; 
        $this->length=$this->end-$this->start;
        $this->noparse = $noparse;
        $this->row=$row; 
        $this->created_at = $created_at; 
        $this->modified_at = $modified_at; 
        $this->deleted_at = $deleted_at; 

    }
    
    /**
    * Get the id of the expression
    *
    * @return int The id of the expression
    */
    public function getId() { 
        return $this->id; 
    }
    
    /**
    * Get the fileid of the expression
    *
    * @return int The fileid of the expression
    */
    public function getFileId() { 
        return $this->file_id; 
    }
    
    /**
    * Set the fileid of the expression
    *
    * @param int The new fileid of the expression
    */
    public function setFileId($file_id) { 
        $this->file_id = $file_id;
    }
    
    /**
    * Get the statemet of the expression
    *
    * @return string The statement of the expression
    */
    public function getStatement() { 
        return $this->statement; 
    }
    
    /**
    * Set the statemet of the expression
    *
    * @param string The statemet of the expression
    */
    public function setStatement($statement) { 
        $this->statement = $statement; 
    }
    
    /**
    * Get the result of the expression
    *
    * @return string The result of the expression
    */
    public function getResult() { 
        return $this->result; 
    }
    
    /**
    * Set the result of the expression
    *
    * @param string The result of the expression
    */
    public function setResult($result) { 
        $this->result = $result; 
    }
    
    /**
    * Get the start position of the expression
    *
    * @return int The start position of the expression
    */
    public function getStart() { 
        return $this->start; 
    }
    
    /**
    * Set the start position of the expression
    *
    * @param int The start position of the expression
    */
    public function setStart($start) { 
        $this->start = $start; 
    }
    
    /**
    * Get the end position of the expression
    *
    * @return int The end position of the expression
    */
    public function getEnd() { 
        return $this->end; 
    }
    
    /**
    * Set the end position of the expression
    *
    * @param int The end position of the expression
    */
    public function setEnd($end) { 
        $this->end = $end; 
    }
    
    /**
    * Get the noparse property of the expression
    *
    * @return int The noparse property of the expression
    */
    public function getNoparse() { 
        return $this->noparse; 
    }
    
    /**
    * Set the noparse property of the expression
    *
    * @param int The noparse property of the expression
    */
    public function setNoparse($noparse) { 
        $this->noparse = $noparse; 
    }

    /**
    * Get the row  of the expression
    *
    * @return int The row of the expression
    */
    public function getRow() { 
        return $this->row; 
    }
    
    /**
    * Set the row of the expression
    *
    * @param int The row of the expression
    */
    public function setRow($row) { 
        $this->row = $row; 
    }

    
    /**
    * Get the length of the expression
    *
    * @return int The length of the expression
    */
    public function getLength() { 
        return $this->length;
    }
    
    /**
    * Get the timestamp of when the expression was created.
    * 
    * @return string The timestamp of creation.
    */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
    * Get the timestamp of when the expression was modified.
    * 
    * @return string The timestamp of modification.
    */
    public function getModifiedAt() {
        return $this->modified_at;
    }

    /**
    * Set the timestamp of when the expression was modified.
    * 
    * @param string $modified_at The new timestamp of modification.
    */
    public function setModifiedAt($modified_at) {
        $this->modified_at = $modified_at;
    }

    /**
    * Get the timestamp of when the expression was deleted.
    * 
    * @return string The timestamp of deletion.
    */
    public function getDeletedAt() {
        return $this->deleted_at;
    }

    /**
    * Set the timestamp of when the expression was deleted.
    * 
    * @param string $deleted_at The new timestamp of deletion.
    */
    public function setDeletedAt($deleted_at) {
        $this->deleted_at = $deleted_at;
    }


}
    