<?php
/* 
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
namespace App\Exalert;

class Commands 
{

    const LIST = [
        'start'=>['method'=>'commandStart'], 
        'help'=>['method'=>'commandHelp'], 
        'cur'=>['class'=>'App\Exalert\Cur', 'method'=>'execute'],
    ];
    private $executable = false;
    private $response = "";
    private $arg = [];
    private $className ='';
    private $methodName = '';

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
        if (in_array($name, array_keys(self::LIST))) {
            $this->className = isset(self::LIST[$name]['class']) ?
                self::LIST[$name]['class'] : 
                'this';
            $this->methodName = isset(self::LIST[$name]['method']) ?
                 self::LIST[$name]['method'] : 
                'execute';
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
       
        if($this->className == 'this') {
             return $this->callOwnMethod();
        } else  {
             return $this->callExternaLMethod();
        }
    }


    private function callOwnMethod()
    {
         if (method_exists($this, $this->methodName)) {
            $method = $this->methodName;
            $this->response =  $this->{$this->methodName}($this->arg);
            return true;
         } else {
            $this->response = 'Method ' . $this->methodName . ' does not exist';
            return false;
         }
    }

    private function callExternalMethod()
    {
        $app = app();
        $command = $app->make($this->className);
        $response =  $app->call([$command, $this->methodName],$this->arg);
        $this->response = $response->getContent();
        return $response->getStatus();
    }

    private function commandStart()
    {
        return 'your command was start';//view();
    }

    private function commandHelp()
    {
        return  'your command - help';//view();
    }
}

