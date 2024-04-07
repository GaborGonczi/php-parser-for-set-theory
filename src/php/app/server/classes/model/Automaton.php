<?php
namespace app\server\classes\model;

/**
* Automaton class represents a automaton that contains expressions.
* 
* This class extends the Model class and has properties for the automaton id, expression id, path, and timestamps.
* 
* @package app\server\classes\model
*/
class Automaton extends Model{

    
    /**
    * The automaton picture id.
    * 
    * @var int
    */
    private $id;
    
    /**
    * The expression id which is represented by the automaton.
    * 
    * @var int
    */
    private $expression_id;

    /**
    * The path of the automaton the automaton picture.
    * 
    * @var string
    */
    private $path;
    
    /**
    * The timestamp of when the automaton was created.
    * 
    * @var string
    */
    private $created_at;
    
    /**
    * The timestamp of when the automaton was modified.
    * 
    * @var string
    */
    private $modified_at;
    
    /**
    * The timestamp of when the automaton was deleted.
    * 
    * @var string
    */
    private $deleted_at;
    

    /**
    * Constructor for the Automaton class.
    * 
    * @param int|null $id The automaton id.
    * @param int $expression_id The expression id which is represented by the automaton.
    * @param string $path The path of the automaton the automaton picture.
    * @param string $created_at The timestamp of when the automaton was created.
    * @param string $modified_at The timestamp of when the automaton was modified.
    * @param string $deleted_at The timestamp of when the automaton was deleted.
    */
    public function __construct($id, $expression_id, $path, $created_at, $modified_at, $deleted_at) {
        $this->id = $id;
        $this->expression_id = $expression_id;
        $this->path=$path;
        $this->created_at = $created_at;
        $this->modified_at = $modified_at;
        $this->deleted_at = $deleted_at;
    }

    /**
    * Get the automaton id.
    * 
    * @return int|null The automaton id.
    */
    public function getId() {
        return $this->id;
    }

    /**
    * Get the expression id which is represented by the automaton.
    * 
    * @return int The expression id.
    */
    public function getExpressionId() {
        return $this->expression_id;
    }

    /**
    * Set the expression id which is represented by the automaton.
    * 
    * @param int $expression_id The new expression id.
    */
    public function setExpressionId($expression_id) {
        $this->expression_id =$expression_id; 
    }

    /**
    * Get the path of the automaton picture.
    * 
    * @return string The path.
    */
    public function getPath() {
        return $this->path ;
    }

    /**
    * Set the path of the automaton picture.
    * 
    * @param string The path.
    */
    public function setPath($path) {
        $this->path =$path; 
    }


    /**
    * Get the timestamp of when the automaton was created.
    * 
    * @return string The timestamp of creation.
    */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
    * Get the timestamp of when the automaton was modified.
    * 
    * @return string The timestamp of modification.
    */
    public function getModifiedAt() {
        return $this->modified_at;
    }

    /**
    * Set the timestamp of when the automaton was modified.
    * 
    * @param string $modified_at The new timestamp of modification.
    */
    public function setModifiedAt($modified_at) {
        $this->modified_at = $modified_at;
    }

    /**
    * Get the timestamp of when the automaton was deleted.
    * 
    * @return string The timestamp of deletion.
    */
    public function getDeletedAt() {
        return $this->deleted_at;
    }

    /**
    * Set the timestamp of when the automaton was deleted.
    * 
    * @param string $deleted_at The new timestamp of deletion.
    */
    public function setDeletedAt($deleted_at) {
        $this->deleted_at = $deleted_at;
    }


}
?>