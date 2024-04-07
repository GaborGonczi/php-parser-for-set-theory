<?php
namespace app\server\classes\runnable;


use \app\server\classes\Database;
use \app\server\classes\model\User;

use \utils\Rootfolder;

use \finfo;

class LoadScript extends Runnable
{
    public function __construct(User $authedUser, Database $db)
    {
        parent::__construct($authedUser, $db);
    }

    public function run():string
    {
        
        if(isset($_FILES['load'])){
            $file=$_FILES['load'];
            $file_tmp = $file['tmp_name'];
            $finfo=new finfo(FILEINFO_MIME_TYPE);
            $file_mime_type=$finfo->file($file_tmp);
            
            if($file_mime_type!=="application/json"){
                $_SESSION['messages']['fileerror']='A fájl nem megfelelő típusú';
                 $this->redirectToProgram();
            }
            $data=json_decode(file_get_contents($file_tmp),true);       
            if($data===null){
                $_SESSION['messages']['fileerror']='A fájl hibás.';
                 $this->redirectToProgram();
            }
            if(!isset($data['id'])){
               $_SESSION['messages']['fileerror']='A fájlazonosító hiányzik.';
                $this->redirectToProgram();
            }
            $fileid=$data['id'];

            if(!$this->isFileExistsWithCurrentUserIdAndId($fileid)){
                $_SESSION['messages']['fileerror']='A fájl nem található.';
                 $this->redirectToProgram();
            }   
            if($this->isFileEmpty($fileid)){
                $_SESSION['messages']['fileerror']='A fájl üres';
                 $this->redirectToProgram();
            }
            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']=$fileid;
        
        }
        else if(isset($_GET['id'])){
            $fileid=$_GET['id'];
            if(!$this->isFileExistsWithCurrentUserIdAndId($fileid)){
                $_SESSION['messages']['fileerror']='A fájl nem található.';
            }
            if($this->isFileEmpty($fileid)){
                $_SESSION['messages']['fileerror']='A fájl üres';
                 $this->redirectToProgram();
            }
            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']=$fileid;
            
        }
        else{
            $_SESSION['messages']['fileerror']='Nem várt hiba történt a feltöltés közben';
        }
        $this->redirectToProgram();
        return "";
    
    }

    private function redirectToProgram(){
        $location=Rootfolder::getPath().'/src/php/app/client/page/program.php';
        header("Location:$location");
        exit(0);
    }
    private function isFileEmpty($fileid)
    {
        return !$this->db->isExist('expressions', ['file_id' => $fileid, 'deleted_at' => null]);
        
    }
    private function isFileExistsWithCurrentUserIdAndId($fileid){
        return $this->db->isExist('files',['user_id'=>$this->user->getId(), 'id'=>$fileid, 'deleted_at'=>null]);
        
    }
    private function getFileContent($fileid)
    {
        return $this->db->get('expressions', [
            'file_id' => $fileid,
            'deleted_at' => null
        ]) ?: [];
    }
}