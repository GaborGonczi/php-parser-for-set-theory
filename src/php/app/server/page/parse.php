<?php
header('Content-Type: application/json');
session_start();
$data=['json'=>[]];

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_SESSION['file'])){
        $data=(array)json_decode($_SESSION['file']);
    }
    $stmtdata=json_decode(file_get_contents("php://input"));
    $data['json'][]=$stmtdata;
    if(isset($_SESSION)){
        $_SESSION['file']=json_encode($data);
        echo json_encode($data);  
    }
    
    
}
else if ($_SERVER['REQUEST_METHOD']==='GET') {
    if(isset($_SESSION['file'])){
        $data=(array)json_decode($_SESSION['file']);
        echo json_encode($data);
    }
    
}
