<?php
if($_SERVER['REMOTE_ADDR']==="::1"){
    define('DEV',true);
}
else {
    define('DEV',false);
}