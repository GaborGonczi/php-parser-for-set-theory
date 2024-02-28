<?php

namespace core\parser\dfa;
use app\server\classes\model\User;

class DFADiagramBuilder
{
   private array $states;
   private $dotString;
   private $inputPath;
   private $outputPath;
   private ?User $authedUser=null;


   public function  __construct(User $authedUser){
        $this->states=[];
        $this->dotString="";
        $this->authedUser=$authedUser;
        $this->inputPath=getenv('BASEPATH')."\\graphs\\".$this->authedUser->getUsername()."\\input\\";
        $this->outputPath=getenv('BASEPATH')."\\graphs\\".$this->authedUser->getUsername()."\\output\\";
        $this->createDirs();
   }
   public function createTriplet($fromState,$input,$toState){
    $this->states[]=['from'=>$fromState,'input'=>$input,'to'=>$toState];
   }
   public function generateOutput($filename,$format='png'){
      $this->dotString=$this->header();
      $this->dotString.=$this->body();
      $this->dotString.=$this->footer();
      file_put_contents($this->inputPath.$filename.".dot",$this->dotString);
      exec("dot -T$format "."-Gsize=500 ". $this->inputPath.$filename.".dot"." -o ".$this->outputPath.$filename.".".$format);
   }

   private function header(){
    return "digraph G {\n
        {\n node [margin=0 fontcolor=blue fontsize=32 width=10 height=10 shape=circle style=filled]\n}\n\n";
   }
   private function footer(){
    return '}';
   }
   private function body(){
      $str="";
      $i=1;
      foreach ($this->states as $state) {
         $str.="$state[from] -> $state[to] [label=\"$i. $state[input]\"]\n";
         $i++;
      }
      return $str;
   }
   private function createDirs(){
      if(!is_dir($this->inputPath)){
         mkdir($this->inputPath,0666,true);
      }
      if(!is_dir($this->outputPath)){
         mkdir($this->outputPath,0666,true);
      }
   }
}