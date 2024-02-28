<?php
namespace app\server\classes;


use \app\server\classes\model\User;
use \utils\Rootfolder;

use \app\server\classes\runable\RegisterScript;
use \app\server\classes\runable\LoginScript;

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

    private RegisterScript $registerPage;

    private LoginScript $loginPage;

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
        $this->registerPage=new RegisterScript();
        $this->loginPage=new LoginScript();
    }

    /**
    * Render the login page.
    */
    public function login() {
        echo $this->loginPage->run();
        $_SESSION['messages']=[];
    }

    /**
    * Render the register page.
    */
    public function register() {
        echo $this->registerPage->run();
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
    
        if(!$this->isUserExistWithThisName($username)){
          $_SESSION['messages']['loginerror']='A felhasználónév vagy a jelszó hibás';
          $this->redirectTo('?login');
        }
        $users=$this->getUsersByUsername($username);
        if(count($users)!==1){
            $_SESSION['messages']['loginerror']='A felhasználónév vagy a jelszó hibás';
            $this->redirectTo('?login');
        }
        foreach ($users as $user) {
            $user=new User(...array_values($user));
        }
        if(!$user||!password_verify($password,$user->getPassword())){
            $_SESSION['messages']['loginerror']='A felhasználónév vagy a jelszó hibás';
            $this->redirectTo('?login');
        }
        
        if($user->getFirstLogin()===null) $user->setFirstLogin(date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()));
        $user->setLastLogin(date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()));
        $user->setModifiedAt(date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()));
        if($user->getId()!==getenv('ADMIN_ID')) $user->setModifiedBy(getenv('ADMIN_ID'));
        $this->db->update('users',$user->getAsAssociativeArray(),['id'=>$user->getId()]);
        $_SESSION[$_COOKIE['PHPSESSID']]['authedUser']=json_encode($user);
        $this->redirectTo();
       
        
       
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
            $this->redirectTo('?register');
        }
        if($this->isUserExistWithThisName($username)){
            $_SESSION['messages']['registererror']='A felhasználónév foglalt';
            $this->redirectTo('?register');
        }
        else if($password!==$passwordagain){
            $_SESSION['messages']['registererror']='A két jelszó nem egyezik';
            $this->redirectTo('?register');
        }
        if(!$this->isUserCreated(new User(null,$_POST['username'],password_hash($_POST['password'],PASSWORD_BCRYPT),
        null,null,date('Y-m-d H:i:s',(new DateTime('now'))->getTimestamp()),getenv('ADMIN_ID'),null,null,null,null))) 
        {
            $_SESSION['messages']['registererror']='Sikertelen regisztráció ismeretlen okból';
            $this->redirectTo('?register');
        }
        $this->redirectTo('?login');
    }

    private function redirectTo($url=""){
        $location=Rootfolder::getPath()."/index.php$url";
        header("Location:$location");
        exit(0);
    }

    private function isUserExistWithThisName($username){
        return $this->db->isExist('users',['username'=>$username, 'deleted_at'=>null]);
    }
    private function getUsersByUsername($username){
        return $this->db->get('users',['username'=>$username,'deleted_at'=>null]);
    }
    private function isUserCreated($user){
        return $this->db->insert('users',$user->getAsAssociativeArray());
    }

}
    

