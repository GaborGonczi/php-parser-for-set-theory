<?php
namespace app\server\classes\runable;

use app\server\classes\Database;
use app\server\classes\model\User;
use \utils\Rootfolder;

class LogoutScript extends Runable{
    public function __construct( User $user= null, Database $db = null){
        parent::__construct($user, $db);
    }
    public function run() :string{
        session_unset();
        session_destroy();
        $location=Rootfolder::getPath().'/index.php';
        header("Location:$location");
        exit(0);
    }
}