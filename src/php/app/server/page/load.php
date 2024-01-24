<?php
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/autoloader.php';

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';

require_once dirname(dirname(__FILE__)).'/db.php';

use \app\server\classes\model\File;
use \app\server\classes\model\Expression;
use \app\server\classes\model\Log;
use \app\server\classes\model\User;

header('Content-Type: application/json');
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
if(!isset($_SESSION[$_COOKIE['PHPSESSID']]['authedUser'])){
    $location=rootfolder().'/index.php';
    header("Location:$location");
    exit(1);
}
else{
    $user=unserialize($_SESSION[$_COOKIE['PHPSESSID']]['authedUser']);
}
global $db;

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
                $files=$db->get('files',['user_id'=>$user->getId(),'deleted_at'=>null]);
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
                            $_SESSION['messages']['fileerror']='A fájl üres';
                        }
                    }
                    else{
                        $_SESSION['messages']['fileerror']='A fájl nem található.';
                    }

                }
                else{
                    $_SESSION['messages']['fileerror']='A felhasználónak nincsenek fájljai.';
                }

            }
            else{
                $_SESSION['messages']['fileerror']='A fájlazonosító hiányzik.';
            }
    
        }
        else{
            $_SESSION['messages']['fileerror']='A fájl hibás.';
        }
    }
    else{
        $_SESSION['messages']['fileerror']='A fájl nem megfelelő típusú';
    }

}
else if(isset($_GET['id'])){
    $fileid=$_GET['id'];
    if($db->isExist('files',['user_id'=>$user->getId(),'id'=>$fileid,'deleted_at'=>null])){
        $isNotEmpty=$db->get('expressions',['file_id'=>$fileid,'deleted_at'=>null]);
        if($isNotEmpty!==false){
            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']=$fileid;
        }
        else {
            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']=$fileid;
            $_SESSION['messages']['fileerror']='A fájl üres';
        }
    }
    else{
        $_SESSION['messages']['fileerror']='A fájl nem található.';
    }
    
}
else{
    $_SESSION['messages']['fileerror']='Nem várt hiba történt a feltöltés közben';
}
$location=rootfolder().'/src/php/app/client/page/program.php';
header("Location:$location");
exit(0);