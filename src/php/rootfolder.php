<?php

function rootfolder(){
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