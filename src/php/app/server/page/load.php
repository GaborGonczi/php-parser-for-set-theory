<?php
require_once dirname(dirname(__FILE__)).'/db.php';
require_once dirname(dirname(dirname(__FILE__))).'/client/constants.php';
require_once dirname(dirname(__FILE__)).'/class/model/Expression.php';
require_once dirname(dirname(__FILE__)).'/class/model/File.php';
require_once dirname(dirname(__FILE__)).'/class/model/Log.php';
require_once dirname(dirname(__FILE__)).'/class/model/User.php';

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


if(isset($_FILES['load'])){
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
else{
    $_SESSION['messages']['fileerror']='Nem várt hiba történt a feltöltés közben';
}
           