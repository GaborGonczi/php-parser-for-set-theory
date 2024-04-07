<?php
namespace app\server\classes\runnable;


use \app\server\classes\Database;
use \app\server\classes\model\User;
use \app\server\classes\model\File;

use \DateTime;

class NewScript extends Runnable
{
    public function __construct(User $authedUser, Database $db)
    {
        parent::__construct($authedUser, $db);
    }

    public function run():string
    {
        
        unset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId']);
        if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])) {
           $this->createNewFile();
        }
        return (string)json_encode(null);
    }

    private function createNewFile() 
    {
        $file = new File(null, $this->user->getId(), date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp()), null);
        $id = $this->db->insert('files', $file->getAsAssociativeArray());
        if ($id) {
            $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'] = $id;
        }
    }
}
