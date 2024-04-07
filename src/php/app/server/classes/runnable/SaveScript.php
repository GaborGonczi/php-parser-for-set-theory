<?php
namespace app\server\classes\runnable;


use \app\server\classes\Database;
use \app\server\classes\model\User;
use \app\server\classes\model\Expression;

use \utils\Rootfolder;

class SaveScript extends Runnable
{

    private array $content;

    public function __construct(User $authedUser, Database $db)
    {
        parent::__construct($authedUser, $db);
        $this->content = [];
    }

    public function run():string
    {
        
        if (!isset($_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'])) {
            $_SESSION['messages']['fileerror'] = 'A fájlazonosító hiányzik.';
            $this->redirectToProgram();
            return "";
        }
        $fileid = $_SESSION[$_COOKIE['PHPSESSID']]['currentFileId'];
        if (!$this->isFileExistsWithCurrentUserIdAndId($fileid)) {
            $_SESSION['messages']['fileerror'] = 'A fájl nem található';
            $this->redirectToProgram();
            return "";
        }
        $content['id'] = $fileid;
        if (!$this->isFileNotEmpty($fileid)) {
            $_SESSION['messages']['fileerror'] = 'A fájl üres';
            $this->redirectToProgram();
            return "";
        }
        $expressions = $this->getFileContent($fileid);
        foreach ($expressions as $expression) {
            $content['expressions'][] = new Expression(...array_values($expression));
        }
        return json_encode($content);

    }

    private function isFileExistsWithCurrentUserIdAndId($fileid){
        return $this->db->isExist('files',['user_id'=>$this->user->getId(), 'id'=>$fileid, 'deleted_at'=>null]);
    }

    private function isFileNotEmpty($fileid)
    {
        return $this->db->isExist('expressions', ['file_id' => $fileid, 'deleted_at' => null]);
    }

    private function getFileContent($fileid)
    {
        return $this->db->get('expressions', [
            'file_id' => $fileid,
            'deleted_at' => null
        ]) ?: [];
    }
    private function redirectToProgram(){
        $location=Rootfolder::getPath().'/src/php/app/client/page/program.php';
        header("Location:$location");
        exit(0);
    }
}