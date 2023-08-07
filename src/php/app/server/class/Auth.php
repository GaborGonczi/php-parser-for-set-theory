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
        echo "loginhandle";
        $userExist=$this->db->isExist('users',['username'=>$_POST['username'],'password'=>$_POST['password']]);
        if($userExist){
            $user=$this->db->get('users',['username'=>$_POST['username'],'password'=>$_POST['password']]);
            if($user){
                $_SESSION['authedUser']=new User(...array_values($user));
            }
           
        }
        else{
            $_SESSION['messages']['loginerror']='A felhasználónév vagy a jelszó hibás';
        }
        $location=rootfolder().'/index.php';
        header("Location:$location");
        
        
    }
    public function registerHadle()  {
        
    }
}
    

