<?php
namespace utils;

/**
* Returns the root folder of the application, based on the server protocol, name, and URI.
*
* @return string The root folder of the application, in the format "protocol://server_name/relative_path".
*/
 class Rootfolder {
   public static function getPath(){
      $method=$_SERVER['SERVER_PORT']===443?'https://':'http://';
      $relative_to_src="";
      
      if(strstr($_SERVER['REQUEST_URI'],'/src',true)!==false){
         $relative_to_src=strstr($_SERVER['REQUEST_URI'],'/src',true);
      }
      else if(strstr($_SERVER['REQUEST_URI'],'/index',true)!==false){
         $relative_to_src=strstr($_SERVER['REQUEST_URI'],'/index',true);
      }
      else{
         $relative_to_src=substr($_SERVER['REQUEST_URI'],0,-1);
      }
      return $method.$_SERVER['SERVER_NAME'].$relative_to_src;
     }
}

