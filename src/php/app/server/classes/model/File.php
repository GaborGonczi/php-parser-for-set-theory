<?php
namespace app\server\classes\model;

/**
* File class represents a file that contains expressions.
* 
* This class extends the Model class and has properties for the file id, user id, and timestamps.
* 
* @package app\server\classes\model
*/
class File extends Model{

    
    /**
    * The file id.
    * 
    * @var int
    */
    private $id;
    
    /**
    * The user id that owns the file.
    * 
    * @var int
    */
    private $user_id;
    
    /**
    * The timestamp of when the file was created.
    * 
    * @var string
    */
    private $created_at;
    
    /**
    * The timestamp of when the file was modified.
    * 
    * @var string
    */
    private $modified_at;
    
    /**
    * The timestamp of when the file was deleted.
    * 
    * @var string
    */
    private $deleted_at;
    

    /**
    * Constructor for the File class.
    * 
    * @param int|null $id The file id.
    * @param int $user_id The user id that owns the file.
    * @param string $created_at The timestamp of when the file was created.
    * @param string $modified_at The timestamp of when the file was modified.
    * @param string $deleted_at The timestamp of when the file was deleted.
    */
    public function __construct($id, $user_id, $created_at, $modified_at, $deleted_at) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->created_at = $created_at;
        $this->modified_at = $modified_at;
        $this->deleted_at = $deleted_at;
    }

    /**
    * Get the file id.
    * 
    * @return int|null The file id.
    */
    public function getId() {
        return $this->id;
    }

    /**
    * Get the user id that owns the file.
    * 
    * @return int The user id.
    */
    public function getUserId() {
        return $this->user_id ;
    }

    /**
    * Set the user id that owns the file.
    * 
    * @param int $user_id The new user id.
    */
    public function setUserId($user_id) {
        $this->user_id =$user_id; 
    }

    /**
    * Get the timestamp of when the file was created.
    * 
    * @return string The timestamp of creation.
    */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
    * Get the timestamp of when the file was modified.
    * 
    * @return string The timestamp of modification.
    */
    public function getModifiedAt() {
        return $this->modified_at;
    }

    /**
    * Set the timestamp of when the file was modified.
    * 
    * @param string $modified_at The new timestamp of modification.
    */
    public function setModifiedAt($modified_at) {
        $this->modified_at = $modified_at;
    }

    /**
    * Get the timestamp of when the file was deleted.
    * 
    * @return string The timestamp of deletion.
    */
    public function getDeletedAt() {
        return $this->deleted_at;
    }

    /**
    * Set the timestamp of when the file was deleted.
    * 
    * @param string $deleted_at The new timestamp of deletion.
    */
    public function setDeletedAt($deleted_at) {
        $this->deleted_at = $deleted_at;
    }


}
?>