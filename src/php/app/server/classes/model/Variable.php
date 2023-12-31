<?php
namespace app\server\classes\model;

/**
* A class that represents a variable in a file.
*/
class Variable extends Model{

/**
* The id of the variable.
* @var int|null
*/
private $id;

/**
* The id of the file that contains the variable.
* @var int
*/
private $file_id;

/**
* The name of the variable.
* @var string
*/
private $name;

/**
* The value of the variable.
* @var mixed
*/
private $value;

/**
* The date and time when the variable was created.
* @var string
*/
private $created_at; 

/**
* The date and time when the variable was last modified.
* @var string
*/
private $modified_at; 

/**
* The date and time when the variable was deleted.
* @var string
*/
private $deleted_at;

/**
* The constructor of the variable class.
* @param int|null $id The id of the variable.
* @param int $file_id The id of the file that contains the variable.
* @param string $name The name of the variable.
* @param mixed $value The value of the variable.
* @param string $created_at The date and time when the variable was created.
* @param string $modified_at The date and time when the variable was last modified.
* @param string $deleted_at The date and time when the variable was deleted.
*/
public function __construct($id, $file_id, $name,$value,  $created_at, $modified_at,$deleted_at) {

$this->id = $id; 
$this->file_id = $file_id; 
$this->name=$name;
$this->value=$value;
$this->created_at = $created_at; 
$this->modified_at = $modified_at; 
$this->deleted_at = $deleted_at; 

}

/**
* Gets the id of the variable.
* @return int|null The id of the variable.
*/
public function getId() { 
return $this->id; 
}

/**
* Gets the id of the file that contains the variable.
* @return int The id of the file that contains the variable.
*/
public function getFileId() { 
return $this->file_id; 
}

/**
* Sets the id of the file that contains the variable.
* @param int $file_id The id of the file that contains the variable.
*/
public function setFileId($file_id) { 
$this->file_id = $file_id;
}

/**
* Gets the name of the variable.
* @return string The name of the variable.
*/
public function getName() { 
return $this->name; 
}

/**
* Sets the name of the variable.
* @param string $name The name of the variable.
*/
public function setName($name) { 
$this->name = $name;
}

/**
* Gets the value of the variable.
* @return mixed The value of the variable.
*/
public function getValue() { 
return $this->value; 
}

/**
* Sets the value of the variable.
* @param mixed $value The value of the variable.
*/
public function setValue($value) { 
$this->value = $value;
}

/**
* Gets the date and time when the variable was created.
* @return string The date and time when the variable was created.
*/
public function getCreatedAt() {
return $this->created_at;
}

/**
* Gets the date and time when the variable was last modified.
* @return string The date and time when the variable was last modified.
*/
public function getModifiedAt() {
return $this->modified_at;
}

/**
* Sets the date and time when the variable was last modified.
* @param string $modified_at The date and time when the variable was last modified.
*/
public function setModifiedAt($modified_at) {
$this->modified_at = $modified_at;
}

/**
* Gets the date and time when the variable was deleted.
* @return string The date and time when the variable was deleted.
*/
public function getDeletedAt() {
return $this->deleted_at;
}

/**
* Sets the date and time when the variable was deleted.
* @param string $deleted_at The date and time when the variable was deleted.
*/
public function setDeletedAt($deleted_at) {
$this->deleted_at = $deleted_at;
}

}