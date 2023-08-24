<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/autoloader.php';

require_once dirname(dirname(__FILE__)).'/db.php';

use \app\server\classes\model\File;
use \app\server\classes\model\Expression;
use \app\server\classes\model\Log;
use \app\server\classes\model\User;

header('Content-Type: application/json');
session_start();
if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $location=rootfolder().'/index.php';
    header("Location:$location");
    exit(1);
}
else{
    $user=unserialize($_SESSION[$_COOKIE['PHPSESSID']]['authedUser']);
}
global $db;

$errormessage="";
if(isset($_FILES['load'])){
    $filefound=false;
    $file=$_FILES['load'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_error = $file['error'];
    $finfo=new finfo(FILEINFO_MIME_TYPE);
    $pathinfo=pathinfo($file_tmp);
    $file_mime_type=$finfo->file($file_tmp);
    
    if($file_mime_type==="application/json"){
        $data=json_decode(file_get_contents($file_tmp),true);       
        if($data!==null){
            if(isset($data['id'])){
                $fileid=$data['id'];
                $files=$db->get('files',['user_id'=>$user->getId()]);
                if($files){
                    foreach ($files as $file) {
                        $filemodel=new File(...array_values($file));
                        if($filemodel->getId()==$fileid){
                            $filefound=true;
                            break;
                        }
                        
                    }
                    if($filefound){
                        $isNotEmpty=$db->isExist('expressions',['file_id'=>$fileid]);
                        if($isNotEmpty){
                            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']=$fileid;
                        }
                        else {
                            $errormessage='A fájl üres';
                        }
                    }
                    else{
                        $errormessage='A fájl nem található.';
                    }

                }
                else{
                    $errormessage='A felhasználónak nincsenek fájljai.';
                }

            }
            else{
                $errormessage='A fájlazonosító hiányzik.';
            }
    
        }
        else{
            $errormessage='A fájl hibás.';
        }
    }
    else{
        $errormessage='A fájl nem megfelelő típusú';
    }

}
else{
    $errormessage='Nem várt hiba történt a feltöltés közben';
}
if($errormessage!==""){
    echo json_encode(["error"=>$errormessage]);
}