<?php
namespace app\server\classes;

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/rootfolder.php';
require_once dirname(dirname(__FILE__)).'/constants.php';

use \app\server\classes\model\User;
use \DateTime;

/**
* Auth class represents the authentication and authorization logic for the application.
* 
* This class has a Database object as a property and uses it to perform login and register operations.
* It also has methods to render the login and register pages and handle the form submissions.
* It uses sessions and cookies to store the user information and messages.
* 
* @package app\\server\\classes
*/
class Auth 
{
    /**
    * The Database object to use for database operations.
    * 
    * @var Database
    * @access private
    */
    private Database $db;

    /**
    * Constructor for the Auth class.
    * 
    * This method sets the Database object as a property of the Auth object.
    * If no Database object is passed as a parameter, it uses null as a default value.
    * 
    * @param Database|null $db The Database object to use for authentication and authorization operations. Default is null.
    */
    public function __construct(Database $db = null) {
        $this->db = $db;
    }

    /**
    * Render the login page.
    */
    public function login() {
        require_once dirname(dirname(__FILE__)).'/page/'.__FUNCTION__.'.php';
        $_SESSION['messages']=[];
    }

    /**
    * Render the register page.
    */
    public function register() {
        require_once dirname(dirname(__FILE__)).'/page/'.__FUNCTION__.'.php';
        $_SESSION['messages']=[];
    }

    /**
    * Handle the login form submission.
    * 
    * This method validates the username and password from the POST data and checks if they match a record in the users table.
    * If they do, it creates a User object and stores it in the session and cookie. It also updates the first login, last login, and modified at fields of the user record.
    * If they don't, it sets an error message in the session. It then redirects to the index page.
    */
    public function loginHandle() {
        $username=htmlspecialchars($_POST['username']);
        $password=htmlspecialchars($_POST['password']);
        $userExist=$this->db->isExist('users',['username'=>$username]);
        if($userExist){
            $users=$this->db->get('users',['username'=>$username,'deleted_at'=>null]);
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
                    if($user->getId()!==1) $user->setModifiedBy(1);
                    $this->db->update('users',$user->getAsAssociativeArray(),['id'=>$user->getId()]);
                    $_SESSION[$_COOKIE['PHPSESSID']]['authedUser']=serialize($user);
                    $location=rootfolder().'/index.php';
                    header("Location:$location");
                    exit(0);
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
        $location=rootfolder().'/index.php?login';
        header("Location:$location");
        exit(0);
    }

    /**
    * Handle the register form submission.
    * 
    * This method validates the username and password from the POST data and checks if they are available and match.
    * If they do, it creates a new User object and inserts it into the users table. It then redirects to the index page.
    * If they don't, it sets an error message in the session.
    */
    public function registerHandle()  {
        $username=htmlspecialchars($_POST['username']);
        $password=htmlspecialchars($_POST['password']);
        $passwordagain=htmlspecialchars($_POST['passwordagain']);
        if($_POST['username']==""||$_POST['password']==""||$_POST['passwordagain']==""){
            $_SESSION['messages']['registererror']='Minden mező kitöltése szükséges';
            $location=rootfolder().'/index.php?register';
        }
        else{
            $userExist=$this->db->isExist('users',['username'=>$username,'deleted_at'=>null]);
            if($userExist){
                $_SESSION['messages']['registererror']='A felhasználónév foglalt';
                $location=rootfolder().'/index.php?register';
            }
            else if($password!==$passwordagain){
                $_SESSION['messages']['registererror']='A két jelszó nem egyezik';
                $location=rootfolder().'/index.php?register';
                
            }
            else{
                $newUser=new User(null,$_POST['username'],password_hash($_POST['password'],PASSWORD_BCRYPT),null,null,date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),
                1,null,null,null,null);
               $newid=$this->db->insert('users',$newUser->getAsAssociativeArray());
                if(!$newid){
                    $_SESSION['messages']['registererror']='Sikertelen regisztráció ismeretlen okból';
                    $location=rootfolder().'/index.php?register';
                }
                else{
                    $location=rootfolder().'/index.php?login';
                    
                }
            }
        }
        header("Location:$location");
        exit(0);
    }
}
    

