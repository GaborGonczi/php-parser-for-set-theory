<?php
namespace app\server\classes\runnable;

use \app\server\classes\model\User;
use \app\server\classes\Database;

use \utils\Rootfolder;

abstract class Runnable {
    protected ?User $user;
    protected ?Database $db;
    protected string $lang;

    public function __construct(User $user=null,Database $db=null,string $lang='hun'){
        $this->user=$user;
        $this->db=$db;
        $this->lang=$lang;
    }

    abstract public function run():string;

    public function getLang(){
        return $this->lang;
    }

}