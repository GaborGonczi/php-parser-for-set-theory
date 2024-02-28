<?php
namespace app\server\classes;

use \app\server\classes\model\User;
use \utils\Rootfolder;

/**
* Application class represents the logic for rendering the client pages of the application.
* 
* This class has a Database object and a User object as properties and uses them to perform operations related to the client pages.
* It also has methods to render the client, program, and help pages by requiring the corresponding files.
* 
* @package app\\server\\classes
*/
class Application
{
    /**
    * The Database object to use for database operations.
    * 
    * @var \\app\\server\\classes\\Database
    * @access private
    */
    private Database $db;

    /**
    * The User object that represents the authenticated user.
    * 
    * @var \app\server\classes\model\User
    * @access private
    */
    private User $authedUser;

    /**
    * Constructor for the Application class.
    * 
    * This method sets the User object and the Database object as properties of the Application object.
    * They are used to perform operations related to the client pages.
    * 
    * @param User $authedUser The User object that represents the authenticated user.
    * @param Database $db The Database object to use for database operations.
    */
    public function __construct(User $authedUser, Database $db) {
        $this->authedUser = $authedUser;
        $this->db=$db;
    }

    /**
    * Render the client page.
    */
    public function client()  {
        require_once dirname(dirname(dirname(__FILE__))).'/client/page/'.__FUNCTION__.'.php';
    }

    /**
    * Render the program page.
    */
    public function program()  {
        require_once dirname(dirname(dirname(__FILE__))).'/client/page/'.__FUNCTION__.'.php';
    }

    /**
    * Render the help page.
    */
    public function help()  {
        require_once dirname(dirname(dirname(__FILE__))).'/client/page/'.__FUNCTION__.'.php';
    }

    public function questionnaire()  {
        require_once dirname(dirname(dirname(__FILE__))).'/client/page/'.__FUNCTION__.'.php';
    }

    public function files()  {
        require_once dirname(dirname(dirname(__FILE__))).'/client/page/'.__FUNCTION__.'.php';
    }
    public function logout()  {
        $location=Rootfolder::getPath().'/src/php/app/server/page/backendentry.php?page=logout';
        header("Location:$location");
    }
}