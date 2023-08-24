<?php
namespace app\server\classes\model;

class User extends Model {

    private $id;
    private $username;
    private $password;
    private $first_login;
    private $last_login;
    private $created_at;
    private $created_by;
    private $modified_at;
    private $modified_by;
    private $deleted_at;
    private $deleted_by;

    public function __construct($id, $username, $password, $first_login, $last_login, $created_at, $created_by, $modified_at, $modified_by, $deleted_at, $deleted_by) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->first_login = $first_login;
        $this->last_login = $last_login;
        $this->created_at = $created_at;
        $this->created_by = $created_by;
        $this->modified_at = $modified_at;
        $this->modified_by = $modified_by;
        $this->deleted_at = $deleted_at;
        $this->deleted_by = $deleted_by;
    }


    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getFirstLogin() {
        return $this->first_login;
    }

    public function setFirstLogin($first_login) {
        $this->first_login = $first_login;
    }

    public function getLastLogin() {
        return $this->last_login;
    }

    public function setLastLogin($last_login) {
        $this->last_login = $last_login;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getCreatedBy() {
        return $this->created_by;
    }

    public function getModifiedAt() {
        return $this->modified_at;
    }

    public function setModifiedAt($modified_at) {
        $this->modified_at = $modified_at;
    }

    public function getModifiedBy() {
        return $this->modified_by; 
    }

    public function setModifiedBy($modified_by) {
        $this->modified_by = $modified_by; 
    }

    public function getDeletedAt() {
        return $this->deleted_at;
    }

    public function setDeletedAt($deleted_at) {
        $this->deleted_at = $deleted_at;
    }

    public function getDeletedBy() {
        return $this->deleted_by;
    }

    public function setDeletedBy($deleted_by) {
        $this->deleted_by= $deleted_by; 
    }

}
?>
