<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';
require_once dirname(__FILE__).'/Database.php';
require_once dirname(__FILE__).'/model/User.php';
class Auth 
{
    private Database $db;
    public function __construct(Database $db = null) {
        $this->db = $db;
    }

    public function login() {
        require_once dirname(dirname(__FILE__)).'/page/'.__FUNCTION__.'.php';
    }
    public function register() {
        require_once dirname(dirname(__FILE__)).'/page/'.__FUNCTION__.'.php';
    }
    public function loginHandle() {

        $userExist=$this->db->isExist('users',['username'=>$_POST['username'],'password'=>$_POST['password']]);
        if($userExist){
            $user=$this->db->get('users',['username'=>$_POST['username'],'password'=>$_POST['password']]);
            if($user){
                if($user['first_login']===null) $user['first_login']=date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp());
                $user['last_login']=date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp());
                $user['modified_at']=date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp());
                $user['modified_by']=1;
                $this->db->update('users',$user,['id'=>$user['id']]);
                $_SESSION['authedUser']=new User(...array_values($user));
            }
           
        }
        else{
            $_SESSION['messages']['loginerror']='A felhasználónév vagy a jelszó hibás';
        }
        $location=rootfolder().'/index.php';
        header("Location:$location");
        
        
    }
    public function registerHandle()  {
       
        $userExist=$this->db->isExist('users',['username'=>$_POST['username'],'password'=>$_POST['password']]);
        if($userExist){
            $_SESSION['messages']['registererror']='A felhasználónév foglalt';
        }
        else{
            $newid=$this->db->insert('users',[
                'username'=>$_POST['username'],
                'password'=>$_POST['password'],
                'first_login'=>null,
                'last_login'=>null,
                'created_at'=>date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),
                'created_by'=>1,
                'modified_at'=>null,
                'modified_by'=>null,
                'deleted_at'=>null,
                'deleted_by'=>null,

            ]);
            if(!$newid){
                $_SESSION['messages']['registererror']='Sikertelen regisztráció';
            }
            $location=rootfolder().'/index.php';
            header("Location:$location");
        }
    }
}
    

