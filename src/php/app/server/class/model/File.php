<?php
require_once dirname(__FILE__).'/Model.php';
class File extends Model{

    private $id;
    private $user_id;
    private $created_at;
    private $modified_at;
    private $deleted_at;


    public function __construct($id, $user_id, $created_at, $modified_at, $deleted_at) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->created_at = $created_at;
        $this->modified_at = $modified_at;
        $this->deleted_at = $deleted_at;
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id ;
    }

    public function setUserId($user_id) {
        $this->user_id =$user_id; 
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
?>