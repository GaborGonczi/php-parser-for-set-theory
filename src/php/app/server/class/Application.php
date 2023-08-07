<?php
require_once dirname(__FILE__).'/Database.php';
require_once dirname(__FILE__).'/model/User.php';
class Application
{
    private Database $db;
    private User $authedUser;
    public function __construct(User $authedUser, Database $db) {
        $this->authedUser = $authedUser;
        $this->db=$db;
    }
    public function client()  {
        require_once dirname(dirname(dirname(__FILE__))).'/client/page/'.__FUNCTION__.'.php';
    }
    public function program()  {
        require_once dirname(dirname(dirname(__FILE__))).'/client/page/'.__FUNCTION__.'.php';
    }
    public function help()  {
        require_once dirname(dirname(dirname(__FILE__))).'/client/page/'.__FUNCTION__.'.php';
    }
}
