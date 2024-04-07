<?php
namespace app\server\classes\runnable;

use \app\server\classes\model\User;
use \app\server\classes\Database;

use \utils\Rootfolder;

abstract class Runnable {
    protected ?User $user;
    protected ?Database $db;

    public function __construct($user=null,$db=null){
        $this->user=$user;
        $this->db=$db;
    }

    abstract public function run():string;

}