<?php
namespace app\server\classes\runnable;


use \app\server\classes\Database;
use \app\server\classes\model\User;

use \core\Regexp;
use utils\Lang;
use \utils\Copier;

use \DateTime;

class CrudapiScript extends Runnable {
    
    private Copier $copier;

    public function __construct(User $authedUser,Database $db,string $lang='hun')  {
        parent::__construct($authedUser,$db,$lang);
        $this->copier=new Copier($this->user,$this->db,$this->lang);

    }

    public function run():string{
        $realparams= substr($_SERVER['QUERY_STRING'], strpos($_SERVER['QUERY_STRING'], "&") + 1);
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if((new Regexp('(files\/)'))->test($realparams)){
                return json_encode($this->getAllFileInfo());
            }
            else if((new Regexp('(automatons\/)'))->test($realparams)){
                return json_encode($this->getAllAutomatonInfo());
            }
            else {
               return json_encode(Lang::getString('missingResourceStart',$this->lang)." $_GET[page] ".Lang::getString('missingResourceEnd',$this->lang));     
            }
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if((new Regexp('(files\/)([1-9][0-9]*)'))->test($realparams)){
                $params=explode('/',$_SERVER['QUERY_STRING']);
                $id=intval(end($params));
                $messages=$this->copier->copy($id);
                if($messages['message']!==""){
                    return json_encode($messages);
                }
                return json_encode(['message'=>Lang::getString('fileCopySuccess', $this->lang)]);
            }
            
        }
        else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
             if ((new Regexp('(files\/)([1-9][0-9]*)'))->test($realparams)) {
                $params=explode('/',$_SERVER['QUERY_STRING']);
                $id=intval(end($params));
               return json_encode($this->deleteFileById($id));
            }
            else if ((new Regexp('(automatons\/)([1-9][0-9]*)'))->test($realparams)) {
                $params=explode('/',$_SERVER['QUERY_STRING']);
                $id=intval(end($params));
                return json_encode($this->deleteAutomatonById($id));
            }
        }
        else if($_SERVER['REQUEST_METHOD'] === 'PATCH'){
            if ((new Regexp('(files\/)([1-9][0-9]*)'))->test($realparams)) {
               $params=explode('/',$_SERVER['QUERY_STRING']);
               $id=intval(end($params));
               $data=json_decode(file_get_contents("php://input"),true);
               unset($data['id']);
               return json_encode($this->updateFileById($id,$data));
           }
    
        }
        return "";
    }

    private function getFileContent($fileid)
    {
        return $this->db->get('expressions', [
            'file_id' => $fileid,
            'deleted_at' => null
        ]) ?: [];
    }

    private function getAllFileInfo(){
        $user_arr=$this->getUserFileInfo();
        $example_arr=array_filter($this->getExampleFileInfo(),function ($arr) {
            return $arr['example']==1&&$arr['user_id']!=$this->user->getId();
        });
        return ['files'=>["my"=>$user_arr,"shared"=>array_values($example_arr)],'uid'=>$this->user->getId(),'messages'=>['noFiles'=>Lang::getString('noFiles',$this->lang)]];
    }
    private function getUserFileInfo() {
        return $this->db->get(['users','files'],['users.id'=>$this->user->getId(),'files.deleted_at'=>null],['files.user_id'=>'`users`.id'],'files.*')?:[];
    }

    private function getExampleFileInfo(){
        return $this->db->get(['files'],['files.example'=>1,'files.deleted_at'=>null],[],'files.*')?:[];
    }

    private function getAllAutomatonInfo() {
        $automatons=$this->db->get(['users','files','expressions','automatons'],['users.id'=>$this->user->getId(),'files.deleted_at'=>null,'expressions.deleted_at'=>null,'automatons.deleted_at'=>null],['files.user_id'=>'`users`.id','expressions.file_id'=>'`files`.id','automatons.expression_id'=>'`expressions`.id'],'expressions.statement,automatons.*')?:[];
        return ['automatons'=>$automatons,'messages'=>['noDiagrams'=>Lang::getString('noDiagrams',$this->lang)]];
    }

    private function deleteFileById($id){
        
        $expressions=$this->getFileContent($id);
        foreach ($expressions as $expression) {
           $expression=(array)$expression;
           if(!$this->deleteExpressionById($expression['id'])){
                return ['message'=>Lang::getString('generalCrudError',$this->lang)];
           }
        }
        if(!$this->deleteFileRecordById($id)){
            return ['message'=>Lang::getString('generalCrudError',$this->lang)];
        }
        else{
            unset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']);
            return ['message'=>Lang::getString('deleteFileSuccessfullyCrud',$this->lang)];
           
        }
    }

    private function deleteAutomatonById($id){
        
        if(!$this->deleteAutomatonRecordById($id)){
           return  ['message'=>Lang::getString('generalCrudError',$this->lang)];
        }
        else{
           return ['message'=> Lang::getString('deleteAutomatonSuccessfullyCrud',$this->lang)];
        }
    }

    private function updateFileById($id,$data){
        if(!$this->updateFileRecordById($id,$data)){
            return  ['message'=>Lang::getString('generalCrudError',$this->lang)];
        }
        else{
          return ['message'=>Lang::getString('modifyFileRecordSuccessfullyCrud',$this->lang)];
        }
    }

    private function deleteExpressionById($expressionid){
        return $this->db->delete('expressions',date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),['id'=>$expressionid]);
    }

    private function deleteFileRecordById($fileid){
        return $this->db->delete('files',date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),['id'=>$fileid]);
    }
    private function deleteAutomatonRecordById($id){
        return  $this->db->delete('automatons',date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),['id'=>$id]);
    }

    private function updateFileRecordById($fileid,$data){
       return $this->db->update('files',$data,['id'=>$fileid]);
    }


}
