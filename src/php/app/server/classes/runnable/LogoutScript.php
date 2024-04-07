<?php
namespace app\server\classes\runnable;

use app\server\classes\Database;
use app\server\classes\model\User;
use \utils\Rootfolder;

class LogoutScript extends Runnable{
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