<?php
namespace app\server\classes\runable;


use \app\server\classes\Database;
use \app\server\classes\model\User;

use \core\Regexp;
use \utils\Rootfolder;

use \DateTime;

class CrudapiScript extends Runable {
    
    public function __construct(User $authedUser,Database $db)  {
        parent::__construct($authedUser,$db);

    }

    public function run():string{
        $realparams= substr($_SERVER['QUERY_STRING'], strpos($_SERVER['QUERY_STRING'], "&") + 1);
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if((new Regexp('(files\/get)'))->test($realparams)){
                return json_encode($this->getAllFileInfo());
            }
            else if ((new Regexp('(files\/delete\/)([1-9][0-9]*)'))->test($realparams)) {
                $params=explode('/',$_SERVER['QUERY_STRING']);
                $id=intval(end($params));
                $this->deleteFileById($id);
                return "";
            }
            else {
                $_SESSION['messages']['cruderror']="A(z) $_GET[page] erőforrás nem található";
                $this->redirectTo('files');
                return "";     
            }
        }
        return "";
    }


    private function redirectTo($url){
        $location=Rootfolder::getPath()."/src/php/app/client/page/$url.php";
        header("Location:$location");
        exit(0);
    }

    private function getFileContent($fileid)
    {
        return $this->db->get('expressions', [
            'file_id' => $fileid,
            'deleted_at' => null
        ]) ?: [];
    }

    private function getAllFileInfo() {
        return $this->db->get(['users','files'],['users.id'=>$this->user->getId(),'files.deleted_at'=>null],['files.user_id'=>'`users`.id'],'files.*');
    }

    private function deleteFileById($id){
        
        $expressions=$this->getAllExpressionByFileId($id);
        foreach ($expressions as $expression) {
           $expression=(array)$expression;
           if(!$this->deleteExpressionById($expression['id'])){
                $_SESSION['messages']['cruderror']='Hiba a művelet közben';
                $this->redirectTo('files');
           }
        }
        if(!$this->deleteFileRecordById($id)){
            $_SESSION['messages']['cruderror']='Hiba a művelet közben'; 
        }
        else{
            $_SESSION['messages']['crudsuccess']='A fájl sikeresen törölve'; 
        }
        $this->redirectTo('files');   
    }

    private function getAllExpressionByFileId($fileid) {
        return $this->db->get('expressions',['file_id'=>$fileid, 'deleted_at'=>null]);
    }

    private function deleteExpressionById($expressionid){
        return $this->db->delete('expressions',date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),['id'=>$expressionid]);
    }

    private function deleteFileRecordById($fileid){
        return $this->db->delete('files',date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),['id'=>$fileid]);
    }


}
