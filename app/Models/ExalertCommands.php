<?php
/* 
 * 1.detect command
 * 1.1. check uncomplete command
 * 1.2. send qery for arguments
 * 2.if it is possible to respone :
 * 2.1.do command
 * 2.3. send response
 * 3.if need arguments :
 * 3.1. save tcommands
 * 3.2. send qery for arguments
 *
 */
namespace App\Models;

use App\Models\Exmo;

class ExalertCommands 
{

    const LIST = ['start', 'help', 'cur'];
    private $executable = false;
    private $response = "";
    private $name = '';
    private $arg = [];

    public function __construct($messageText) 
    {
        $this->executable = $this->extractCommand($messageText);
    }
    
    public function isExecutable()
    {
        return $this->executable;
    }

    public function getResponse()
    {
        return $this->response;
    }

    private function extractCommand($text)
    {
       $chunk = explode(' ', $text);
        $name = str_replace('/','',array_shift($chunk));
        if (in_array(self::LIST, $name)) {
            $this->name = $name;
            $this->arg = $chunk;
            return true;
        }
        return false;
    }

    public function execute() 
    {  
        if (!$this->executable) {
            return false;
        }
        echo $this->name;
        print_r($this->arg);
        $this->response = '';
        return true;
    }

}
