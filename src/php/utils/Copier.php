<?php
namespace utils;

use \app\server\classes\Database;
use \app\server\classes\model\User;
use \app\server\classes\model\File;
use \app\server\classes\model\Expression;
use \app\server\classes\model\Variable;

use \DateTime;



class Copier
{
    private Database $db;
    private User $authedUser;
    private string $lang;

    public function __construct(User $authedUser, Database $db, string $lang)
    {
        $this->db = $db;
        $this->authedUser = $authedUser;
        $this->lang = $lang;
    }

    public function copy($fileid)
    {
        $messages = ['message' => ''];
        if ($this->isFileCopyable($fileid)) {
            $filedata = $this->getFile($fileid);
            $expressiondata = $this->getExpressions($fileid);
            $variabledata = $this->getVariables($fileid);
            foreach ($filedata as $file) {
                $file['id'] = null;
                $file['user_id'] = $this->authedUser->getId();
                $file['example']=0;
                $file['created_at'] = date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp());
                $file['modified_at'] = date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp());
                $file['deleted_at'] = null;
                $newfile = new File(...array_values($file));
                $newfileid = $this->createNewFile($newfile->getAsAssociativeArray());
                if (!$newfileid) {
                    $messages['message'] = Lang::getString('fileCopyError', $this->lang);
                    return $messages;
                }
            }
            $failedExpressionCopyCount=0;
            foreach ($expressiondata as $expression) {
                $newexpressiondata = (array) $expression;
                $newexpressiondata['id'] = null;
                $newexpressiondata['file_id'] = $newfileid;
                unset($newexpressiondata['length']);
                $newexpressiondata['created_at'] = date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp());
                $newexpressiondata['modified_at'] = date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp());
                $newexpressiondata['deleted_at'] = null;         
                $newexpression = new Expression(...array_values($newexpressiondata));
                $newexpressionid = $this->createNewExpression($newexpression->getAsAssociativeArray());
                if (!$newexpressionid) {
                    $failedExpressionCopyCount++;
                }
            }
            if($failedExpressionCopyCount){
                $messages['message'] .= Lang::getString('expressionCopyError', $this->lang) .$failedExpressionCopyCount. ' ';
            }
            $failedVariableCopyCount=0;
            foreach ($variabledata as $variable) {
                $variable = (array) $variable;
                $variable['id'] = null;
                $variable['file_id'] = $newfileid;
                $variable['created_at'] = date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp());
                $variable['modified_at'] = date('Y-m-d H:i:s', (new DateTime('now'))->getTimestamp());
                $variable['deleted_at'] = null;
                $newvariable = new Variable(...array_values($variable));
                $newvariableid = $this->createNewVariable($newvariable->getAsAssociativeArray());
                if (!$newvariableid) {
                    $failedVariableCopyCount++;
                }
            }
            if($failedVariableCopyCount){
                $messages['message'] .= Lang::getString('variableCopyError', $this->lang) .$failedVariableCopyCount;
            }
        } 
        else 
        {
            $messages['message']=Lang::getString('fileNotCopyableError',$this->lang).' ';
        }
        return $messages;
    }

    private function isFileCopyable($fileid)
    {
        return $this->db->isExist('files', ['id' => $fileid, 'example' => true]);
    }

    private function getFile($fileid)
    {
        return $this->db->get('files', ['id' => $fileid, 'example' => true]) ?: [];
    }

    private function getExpressions($fileid)
    {
        return $this->db->get('expressions', ['file_id' => $fileid]) ?: [];
    }
    private function getVariables($fileid)
    {
        return $this->db->get('variables', ['file_id' => $fileid]) ?: [];
    }
    private function createNewFile($fileAsArray)
    {
        return $this->db->insert('files', $fileAsArray);
    }
    private function createNewExpression($expressionAsArray)
    {
        unset($expressionAsArray['length']);
        return $this->db->insert('expressions', $expressionAsArray);
    }
    private function createNewVariable($variableAsArray)
    {
        return $this->db->insert('variables', $variableAsArray);
    }
}