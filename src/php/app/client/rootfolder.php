<?php
function rootfolder(){
        $method=$_SERVER['SERVER_PORT']===443?'https://':'http://';
        $relative_to_src=strstr($_SERVER['REQUEST_URI'],'/src',true);
        return $method.$_SERVER['SERVER_NAME'].$relative_to_src;
}