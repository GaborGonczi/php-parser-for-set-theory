<?php
namespace app\server\classes\model;

/**
* Log class represents a user action on an expression in a file.
* 
* This class extends the Model class and has properties for the
* log id, expression id, type, button, ctrlKey, key, source element id, value, tagname, title, time, and timestamp.
* 
* @package app\server\classes\model
*/
class Log extends Model{
   
   /**
   * The log id.
   * 
   * @var int
   */
   private $id;
   
   /**
   * The expression id that the log belongs to.
   * 
   * @var int
   */
   private $expression_id;
   
   /**
   * The type of the user action, such as click, keypress, etc.
   * 
   * @var string
   */
   private $type;
   
   /**
   * The button that was clicked, if any.
   * 
   * @var string|null
   */
   private $button;
   
   /**
   * The flag to indicate if the ctrl key was pressed, if any.
   * 
   * @var bool
   */
   private $ctrlKey;
   
   /**
   * The key that was pressed, if any.
   * 
   * @var string|null
   */
   private $key;
   
   /**
   * The id of the source element that triggered the action, if any.
   * 
   * @var string|null
   */
   private $sourceElementId;
   
   /**
   * The value of the source element that triggered the action, if any.
   * 
   * @var string|null
   */
   private $sourceElementValue;
   
   /**
   * The tag name of the source element that triggered the action, if any.
   * 
   * @var string|null
   */
   private $sourceElementTagname;
   
   /**
   * The title of the source element that triggered the action, if any.
   * 
   * @var string|null
   */
   private $sourceElementTitle;
   
   /**
   * The time of the user action in milliseconds.
   * 
   * @var int
   */
   private $time;
   
   /**
   * The timestamp of when the log was created.
   * 
   * @var string
   */
   private $created_at;
 
   /**
   * Constructor for the Log class.
   * 
   * @param int|null $id The log id.
   * @param int $expression_id The expression id that the log belongs to.
   * @param string $type The type of the user action, such as click, keypress, etc.
   * @param string|null $button The button that was clicked, if any.
   * @param bool $ctrlKey The flag to indicate if the ctrl key was pressed, if any.
   * @param string|null $key The key that was pressed, if any.
   * @param string|null $sourceElementId The id of the source element that triggered the action, if any.
   * @param string|null $sourceElementValue The value of the source element that triggered the action, if any.
   * @param string|null $sourceElementTagname The tag name of the source element that triggered the action, if any.
   * @param string|null $sourceElementTitle The title of the source element that triggered the action, if any.
   * @param int $time The time of the user action in milliseconds.
   * @param string $created_at The timestamp of when the log was created.
   */
   public function __construct($id, $expression_id, $type, $button, $ctrlKey, $key, $sourceElementId, $sourceElementValue, $sourceElementTagname, $sourceElementTitle, $time, $created_at) {
      $this->id = $id; 
      $this->expression_id = $expression_id; 
      $this->type = $type; 
      $this->button = $button; 
      $this->ctrlKey = $ctrlKey; 
      $this->key = $key; 
      $this->sourceElementId = $sourceElementId; 
      $this->sourceElementValue = $sourceElementValue; 
      $this->sourceElementTagname = $sourceElementTagname; 
      $this->sourceElementTitle = $sourceElementTitle;
      $this->time=$time; 
      $this->created_at = $created_at; 
      
   }

   /**
   * Get the log id.
   * 
   * @return int|null The log id.
   */
   public function getId() { 
      return $this->id; 
   }

   /**
   * Get the expression id that the log belongs to.
   * 
   * @return int The expression id.
   */
   public function getExpressionId() { 
      return $this->expression_id;
   }

   /**
   * Set the expression id that the log belongs to.
   * 
   * @param int $expression_id The new expression id.
   */
   public function setExpressionId($expression_id) { 
      $this->expression_id = $expression_id; 
   }

   /**
   * Get the type of the user action, such as click, keypress, etc.
   * 
   * @return string The type of the user action.
   */
   public function getType() { 
     return $this->type; 
   }

   /**
   * Set the type of the user action, such as click, keypress, etc.
   * 
   * @param string $type The new type of the user action.
   */
   public function setType($type) { 
     $this->type = $type; 
   }

   /**
   * Get the button that was clicked, if any.
   * 
   * @return string|null The button that was clicked.
   */
   public function getButton() { 
      return $this->button; 
   }

   /**
   * Set the button that was clicked, if any.
   * 
   * @param string|null $button The new button that was clicked.
   */
   public function setButton($button) { 
      $this->button = $button; 
   }

   /**
   * Get the flag to indicate if the ctrl key was pressed, if any.
   * 
   * @return bool The flag to indicate if the ctrl key was pressed.
   */
   public function getCtrlKey() { 
      return $this->ctrlKey; 
   }

   /**
   * Set the flag to indicate if the ctrl key was pressed, if any.
   * 
   * @param bool $ctrlKey The new flag to indicate if the ctrl key was pressed.
   */
   public function setCtrlKey($ctrlKey) { 
      $this->ctrlKey = $ctrlKey; 
   }

   /**
   * Get the key that was pressed, if any.
   * 
   * @return string|null The key that was pressed.
   */
   public function getKey() { 
      return $this->key; 
   }

   /**
   * Set the key that was pressed, if any.
   * 
   * @param string|null $key The new key that was pressed.
   */
   public function setKey($key) { 
      $this->key = $key; 
   }

   /**
   * Get the id of the source element that triggered the action, if any.
   * 
   * @return string|null The id of the source element.
   */
   public function getSourceElementId() { 
      return $this->sourceElementId; 
   }

   /**
   * Set the id of the source element that triggered the action, if any.
   * 
   * @param string|null $sourceElementId The new id of the source element.
   */
   public function setSourceElementId($sourceElementId) { 
      $this->sourceElementId = $sourceElementId; 
   }

   /**
   * Get the value of the source element that triggered the action, if any.
   * 
   * @return string|null The value of the source element.
   */
   public function getSourceElementValue() { 
      return $this->sourceElementValue; 
   }

   /**
   * Set the value of the source element that triggered the action, if any.
   * 
   * @param string|null $sourceElementValue The new value of the source element.
   */
   public function setSourceElementValue($sourceElementValue) { 
      $this->sourceElementValue = $sourceElementValue; 
   }

   /**
   * Get the tag name of the source element that triggered the action, if any.
   * 
   * @return string|null The tag name of the source element.
   */
   public function getSourceElementTagname() { 
      return $this->sourceElementTagname; 
   }

   /**
   * Set the tag name of the source element that triggered the action, if any.
   * 
   * @param string|null $sourceElementTagname The new tag name of the source element.
   */
   public function setSourceElementTagname($sourceElementTagname) { 
      $this->sourceElementTagname = $sourceElementTagname; 
   }

   /**
   * Get the title of the source element that triggered the action, if any.
   * 
   * @return string|null The title of the source element.
   */
   public function getSourceElementTitle() { 
      return $this->sourceElementTitle; 
   }

   /**
   * Set the title of the source element that triggered the action, if any.
   * 
   * @param string|null $sourceElementTitle The new title of the source element.
   */
   public function setSourceElementTitle($sourceElementTitle) { 
      $this->sourceElementTitle = $sourceElementTitle; 
   }

   /**
   * Get the time of the user action in milliseconds.
   * 
   * @return int The time of the user action in milliseconds.
   */
   public function getTime() {
      return $this->time;
   }

   /**
   * Set the time of the user action in milliseconds.
   * 
   * @param int $time The new time of the user action in milliseconds.
   */
   public function setTime($time) {
      $this->time=$time;
   }

   /**
   * Get the timestamp of when the log was created.
   * 
   * @return string The timestamp of creation.
   */
   public function getCreatedAt() { 
      return $this->created_at; 
   }
}
 
 