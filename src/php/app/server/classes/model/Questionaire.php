<?php
namespace app\server\classes\model;

/**
* A class that represents a questionaire in the database.
* This class extends the Model class and has properties for the user id, answers and timestamps.
* 
* @package app\server\classes\model
*/
class Questionaire extends Model
{
    /**
    * The id of the questionaire.
    * @var int|null
    */
    private $id;

    /**
    * The id of the user who created the questionaire.
    * @var int
    */
    private $user_id;

    /**
    * The answers to the questions in the questionaire.
    * @var string
    */
    private $answers;

    /**
    * The date and time when the questionaire was created.
    * @var string
    */
    private $created_at; 

    /**
    * The date and time when the questionaire was last modified.
    * @var string
    */
    private $modified_at; 

    /**
    * The date and time when the questionaire was deleted.
    * @var string
    */
    private $deleted_at; 

    /**
    * The constructor of the questionaire class.
    * @param int|null $id The id of the questionaire.
    * @param int $user_id The id of the user who created the questionaire.
    * @param string $answers The answers to the questions in the questionaire.
    * @param string $created_at The date and time when the questionaire was created.
    * @param string $modified_at The date and time when the questionaire was last modified.
    * @param string $deleted_at The date and time when the questionaire was deleted.
    */
    public function __construct($id, $user_id, $answers, $created_at, $modified_at, $deleted_at) {

        $this->id = $id; 
        $this->user_id = $user_id; 
        $this->answers = $answers;
        $this->created_at = $created_at; 
        $this->modified_at = $modified_at; 
        $this->deleted_at = $deleted_at; 

    }

    /**
    * Gets the id of the questionaire.
    * @return int|null The id of the questionaire.
    */
    public function getId() { 
        return $this->id; 
    }

    /**
    * Gets the id of the user who created the questionaire.
    * @return int The id of the user who created the questionaire.
    */
    public function getUserId() { 
        return $this->user_id; 
    }

    /**
    * Sets the id of the user who created the questionaire.
    * @param int $user_id The id of the user who created the questionaire.
    */
    public function setUserId($user_id) { 
        $this->user_id = $user_id;
    }

    /**
    * Gets the answers to the questions in the questionaire.
    * @return string The answers to the questions in the questionaire.
    */
    public function getAnswers() { 
        return $this->answers; 
    }

    /**
    * Sets the answers to the questions in the questionaire.
    * @param array $answers The answers to the questions in the questionaire.
    */
    public function setAnswers($answers) { 
        $this->answers = $answers;
    }

    /**
    * Gets the date and time when the questionaire was created.
    * @return string The date and time when the questionaire was created.
    */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
    * Gets the date and time when the questionaire was last modified.
    * @return string The date and time when the questionaire was last modified.
    */
    public function getModifiedAt() {
        return $this->modified_at;
    }

    /**
    * Sets the date and time when the questionaire was last modified.
    * @param string $modified_at The date and time when the questionaire was last modified.
    */
    public function setModifiedAt($modified_at) {
        $this->modified_at = $modified_at;
    }

    /**
    * Gets the date and time when the questionaire was deleted.
    * @return string The date and time when the questionaire was deleted.
    */
    public function getDeletedAt() {
        return $this->deleted_at;
    }

    /**
    * Sets the date and time when the questionaire was deleted.
    * @param string $deleted_at The date and time when the questionaire was deleted.
    */
    public function setDeletedAt($deleted_at) {
        $this->deleted_at = $deleted_at;
    }

}