<?php
namespace app\server\classes\model;

/**
* User class represents a user of the application.
* 
* This class extends the Model class and has properties for the user id, username, password, first and last login times, and timestamps.
* 
* @package app\server\classes\model
*/
class User extends Model {


    /**
    * The user id.
    *
    * @var int|null
    */
    private $id;

    /**
    * The user name.
    *
    * @var string
    */
    private $username;

    /**
    * The user password, hashed with bcrypt algorithm.
    *
    * @var string
    */
    private $password;

    /**
    * The first login date and time of the user in YYYY-MM-DD HH:MM:SS format.
    *
    * @var string|null
    */
    private $first_login;

    /**
    * The last login date and time of the user in YYYY-MM-DD HH:MM:SS format.
    *
    * @var string|null
    */
    private $last_login;

    /**
    * The user's preferred language
    *
    * @var string|null
    */
    private $language;

    /**
    * The timestamp of when the user was created.
    *
    * @var string|null
    */
    private $created_at;

    /**
    * The id of the user who created this user.
    *
    * @var int|null
    */
    private $created_by;

    /**
    * The timestamp of when the user was modified.
    *
    * @var string|null
    */
    private $modified_at;

    /**
    * The id of the user who modified this user.
    *
    * @var int|null
    */
    private $modified_by;

    /**
    * The timestamp of when the user was deleted.
    *
    * @var string|null
    */
    private $deleted_at;

    /**
    * The id of the user who deleted this user.
    *
    * @var int|null
    */
    private $deleted_by;
        

    /**
    * Constructor for the User class.
    *
    * @param int|null $id The user id.
    * @param string $username The user name.
    * @param string $password The user password, hashed with bcrypt algorithm.
    * @param string|null $first_login The first login date and time of the user in YYYY-MM-DD HH:MM:SS format.
    * @param string|null $last_login The last login date and time of the user in YYYY-MM-DD HH:MM:SS format.
    * @param string|null $language The user's preferred language
    * @param string|null $created_at The timestamp of when the user was created.
    * @param int|null $created_by The id of the user who created this user.
    * @param string|null $modified_at The timestamp of when the user was modified.
    * @param int|null $modified_by The id of the user who modified this user.
    * @param string|null $deleted_at The timestamp of when the user was deleted.
    * @param int|null $deleted_by The id of the user who deleted this user.
    */
    public function __construct($id, $username, $password, $first_login, $last_login, $language, $created_at, $created_by, $modified_at, $modified_by, $deleted_at, $deleted_by) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->first_login = $first_login;
        $this->last_login = $last_login;
        $this->language=$language;
        $this->created_at = $created_at;
        $this->created_by = $created_by;
        $this->modified_at = $modified_at;
        $this->modified_by = $modified_by;
        $this->deleted_at = $deleted_at;
        $this->deleted_by = $deleted_by;
    }

    /**
    * Get the id of user.
    *
    * @return int The user id.
    */
    public function getId() {
        return $this->id;
    }

    /**
    * Get the username of user.
    * 
    * @return string The user username.
    */
    public function getUsername() {
        return $this->username;
    }

    /**
    * Set the username of user.
    * 
    * @param string The mew username.
    */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
    * Get the user password.
    * 
    * @return string The new password.
    */
    public function getPassword() {
        return $this->password;
    }

    /**
    * Set the user password.
    * 
    * @param string The user password.
    */
    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
    * Get the first login date and time of the user
    *
    * @return string The first login date and time in YYYY-MM-DD HH:MM:SS format
    */
    public function getFirstLogin() {
        return $this->first_login;
    }

    /**
    * Set the first login date and time of the user
    *
    * @param string The first login date and time in YYYY-MM-DD HH:MM:SS format
    */
    public function setFirstLogin($first_login) {
        $this->first_login = $first_login;
    }

    /**
    * Get the last login date and time of the user
    *
    * @return string The last login date and time in YYYY-MM-DD HH:MM:SS format
    */
    public function getLastLogin() {
        return $this->last_login;
    }

    /**
    * Set the last login date and time of the user
    *
    * @param string The last login date and time in YYYY-MM-DD HH:MM:SS format
    */
    public function setLastLogin($last_login) {
        $this->last_login = $last_login;
    }

    /**
    * Get the user's preferred language
    *
    * @return string The user's preferred language.
    */
    public function getLanguage() {
        return $this->language;
    }

    /**
    * Set the user's preferred language
    *
    * @param string The user's preferred language.
    */
    public function setLanguage($language) {
        $this->language = $language;
    }

    /**
    * Get the timestamp of when the user was created.
    * 
    * @return string The timestamp of creation.
    */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
    * Get the user id that created the user.
    * 
    * @return int The user id that created the user.
    */
    public function getCreatedBy() {
        return $this->created_by;
    }

    /**
    * Get the timestamp of when the user was modified.
    * 
    * @return string The timestamp of modification.
    */
    public function getModifiedAt() {
        return $this->modified_at;
    }

    /**
    * Set the timestamp of when the user was modified.
    * 
    * @param string $modified_at The new timestamp of modification.
    */
    public function setModifiedAt($modified_at) {
        $this->modified_at = $modified_at;
    }

    /**
    * Get the user id that modified the user.
    * 
    * @return int The user id that modified the user.
    */
    public function getModifiedBy() {
        return $this->modified_by; 
    }

    /**
    * Set the user id that modified the user.
    * 
    * @param int $modified_by The new user id that modified the user.
    */
    public function setModifiedBy($modified_by) {
        $this->modified_by = $modified_by; 
    }

    /**
    * Get the timestamp of when the user was deleted.
    * 
    * @return string The timestamp of deletion.
    */
    public function getDeletedAt() {
        return $this->deleted_at;
    }

    /**
    * Set the timestamp of when the user was deleted.
    * 
    * @param string $deleted_at The new timestamp of deletion.
    */
    public function setDeletedAt($deleted_at) {
        $this->deleted_at = $deleted_at;
    }

    /**
    * Get the user id that deleted the user.
    * 
    * @return int The user id that deleted the user.
    */
    public function getDeletedBy() {
        return $this->deleted_by;
    }

    /**
    * Set the user id that deleted the user.
    * 
    * @param int $deleted_by The new user id that deleted the user.
    */
    public function setDeletedBy($deleted_by) {
        $this->deleted_by= $deleted_by; 
    }

}
?>
