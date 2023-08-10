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
        $username=htmlspecialchars($_POST['username']);
        $password=htmlspecialchars($_POST['password']);
        $userExist=$this->db->isExist('users',['username'=>$username]);
        if($userExist){
            $users=$this->db->get('users',['username'=>$username]);
            if(count($users)===1){
                foreach ($users as $user) {
                    $user=new User(...array_values($user));
                }
            }
            else{
                $_SESSION['messages']['loginerror']='A felhasználónév vagy a jelszó hibás';
            }

            if($user){
                if(password_verify($password,$user->getPassword())){
                    if($user->getFirstLogin()===null) $user->setFirstLogin(date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()));
                    $user->setLastLogin(date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()));
                    $user->setModifiedAt(date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()));
                    $user->setModifiedBy(1);
                    $this->db->update('users',$user->getAsAssociativeArray(),['id'=>$user->getId()]);
                    $_SESSION[$_COOKIE['PHPSESSID']]['authedUser']=serialize($user);
                }
                else{
                    $_SESSION['messages']['loginerror']='A felhasználónév vagy a jelszó hibás';
                }
                
            }
            else{
                $_SESSION['messages']['loginerror']='A felhasználónév vagy a jelszó hibás';
            }
           
        }
        else{
            $_SESSION['messages']['loginerror']='A felhasználónév vagy a jelszó hibás';
        }
        $location=rootfolder().'/index.php';
        header("Location:$location");
        
        
    }
    public function registerHandle()  {
        $username=htmlspecialchars($_POST['username']);
        $password=htmlspecialchars($_POST['password']);
        $passwordagain=htmlspecialchars($_POST['passwordagain']);
        $userExist=$this->db->isExist('users',['username'=>$username]);
        if($userExist){
            $_SESSION['messages']['registererror']='A felhasználónév foglalt';
        }
        if($password!==$passwordagain){
            $_SESSION['messages']['registererror']='A két jelszó nem egyezik';
        }
        else{
            $newUser=new User(null,$_POST['username'],password_hash($_POST['password'],PASSWORD_BCRYPT),null,null,date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),
            1,null,null,null,null);
           $newid=$this->db->insert('users',$newUser->getAsAssociativeArray());
            if(!$newid){
                $_SESSION['messages']['registererror']='Sikertelen regisztráció ismeretlen okból';
            }
            $location=rootfolder().'/index.php';
            header("Location:$location");
        }
    }
}
    

