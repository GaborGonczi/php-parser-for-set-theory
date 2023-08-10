<?php
require_once dirname(__FILE__).'/Model.php';
class Log extends Model{

     private $id;
     private $expression_id;
     private $type;
     private $button;
     private $ctrlKey;
     private $key;
     private $sourceElementId;
     private $sourceElementValue;
     private $sourceElementTagname;
     private $sourceElementTitle;
     private $time;
     private $created_at;
    
     // Constructor
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

     public function getId() { 
        return $this->id; 
     }

     public function getExpressionId() { 
        return $this->expression_id;
     }

     public function setExpressionId($expression_id) { 
        $this->expression_id = $expression_id; 
     }

     public function getType() { 
        return $this->type; 
     }

     public function setType($type) { 
        $this->type = $type; 
     }

     public function getButton() { 
        return $this->button; 
     }

     public function setButton($button) { 
        $this->button = $button; 
     }

     public function getCtrlKey() { 
        return $this->ctrlKey; 
     }

     public function setCtrlKey($ctrlKey) { 
        $this->ctrlKey = $ctrlKey; 
     }

     public function getKey() { 
        return $this->key; 
     }

     public function setKey($key) { 
        $this->key = $key; 
     }

     public function getSourceElementId() { 
        return $this->sourceElementId; 
     }

     public function setSourceElementId($sourceElementId) { 
        $this->sourceElementId = $sourceElementId; 
     }

     public function getSourceElementValue() { 
        return $this->sourceElementValue; 
     }

     public function setSourceElementValue($sourceElementValue) { 
        $this->sourceElementValue = $sourceElementValue; 
     }

     public function getSourceElementTagname() { 
        return $this->sourceElementTagname; 
     }

     public function setSourceElementTagname($sourceElementTagname) { 
        $this->sourceElementTagname = $sourceElementTagname; 
     }

     public function getSourceElementTitle() { 
        return $this->sourceElementTitle; 
     }

     public function setSourceElementTitle($sourceElementTitle) { 
         $this->sourceElementTitle = $sourceElementTitle; 
     }

     public function getTime() {
         return $this->time;
     }

     public function setTime($time) {
         $this->time=$time;
     }

     public function getCreatedAt() { 
         return $this->created_at; 
     }
}
 
 