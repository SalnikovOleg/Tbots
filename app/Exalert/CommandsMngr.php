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

use App\Exalert\Tsession;

class CommandsMngr
{

    const WRONG_COMMAND = 'Current is /%s. Send /%s end to finish it';
    const LIST = [
        'start' => ['method'=>'commandStart'], 
        'help' => ['method'=>'commandHelp'], 
        'cur' => ['class'=>'App\Exalert\Cur', 'method'=>'execute'],
        'wtd' => ['class'=>'App\Exalert\Wtd', 'method'=>'getTask'],
        'wtd_new'=> ['class'=>'App\Exalert\Wtd', 'method'=>'newTask'],
        'end' => ['method' => 'commandEnd'],
    ];

    private $response = null;
    private $command = null;
    private $message = null;
    private $tSession = null;

    public function __construct($message) 
    {
        $this->command = (object)['name'=>'','className'=>'', 'methodName'=>'', 'args'=>[]];
        $this->response =(object)['text'=>'', 'buttons'=>[], 'status'=>false];
        $this->message = $message;
        $this->executable = $this->extractCommand($message->text);
        $this->tSession = $this->checkTSession($message);

        if ($this->command->name == 'end') return;

            //resived command no equal current command
        if ($this->executable && $this->tSession->command 
                 && $this->tSession->command != $this->command->name) {
               $this->executable = false;
               $this->response->text = $this->wrongCommand();
               $this->response->status = false;

           //resived  no command. there os notcompleted command
        } elseif (!$this->executable && $this->tSession->command ) {
           $this->executable =  $this->restoreCommand($this->tSession, $this->message);
        }
    }
    
    public function isExecutable()
    {
        return $this->executable;
    }

    public function getResponse()
    {
        return $this->response;
    }
    
    private function wrongCommand() 
    {
       return  sprintf(self::WRONG_COMMAND, 
                        $this->tSession->command, 
                        $this->tSession->command
                    ); 
    }

    private function extractCommand($text)
    {
        $chunk = explode(' ', $text);//erase spaces
        $name =str_replace('/','',array_shift($chunk));
        $this->command->name = $name;
        if (in_array($name, array_keys(self::LIST))) {
            $this->command->className = isset(self::LIST[$name]['class']) ?
                self::LIST[$name]['class'] : 
                'this';
            $this->command->methodName = isset(self::LIST[$name]['method']) ?
                 self::LIST[$name]['method'] : 
                'execute';
            $this->command->args = $chunk;

            return true;
        }
        return false;
    }

    private function checkTSession($message) 
    {
        $tSession = Tsession::firstOrNew([
            'user_id'=> $message->from->id,
            'chat_id'=> $message->chat->id,
            'done'=> false
        ]);
        return $tSession;
    }

    private function restoreCommand($tSession, $message) 
    {
        $text = '/' . $this->tSession->command . ' '
            . ($this->tSession->method ? $this->tSession->method : '')
            . $message->text;

        return $this->extractCommand($text);
    }

    public function execute() 
    {  
        if (!$this->executable) {
            return false;
        }
       
        if($this->command->className == 'this') {
             return $this->callOwnMethod();
        } else  {
             return $this->callExternaLMethod();
        }
    }


    private function callOwnMethod()
    {
         if (method_exists($this, $this->command->methodName)) {
             $this->response->text = $this->{$this->command->methodName}($this->command->args);

            $this->response->status = true;    
         } else {
            $this->response->text = 'Method ' . $this->command->methodName . ' does not exist';
            $this->response->status = false;    
         }
         return $this->response->status;
    }

    /**
     * execute methid of command
     * methodcresive telegram message and args of command from messgae
     */
    private function callExternalMethod()
    {
        $app = app();
        $command = $app->make($this->command->className);
        $result = $app->call([$command, $this->command->methodName],[
            'message'=>$this->message, 
            'args'=>$this->command->args]);
        $this->response = $result->getResponse();
        return $result->getStatus();
    }

    private function commandStart($args)
    {
        return view('exalert.start')->render();
    }

    private function commandHelp($args)
    {
        return  view('exalert.help')->render();
    }

    private function commandEnd()
    {
        if ($this->tSession->command) {
            $this->tSession->done = true;
            $this->tSession->save();
            return $this->tSession->command . ' finished';
        } else {
            return 'No method to finish';
        }
    }
}

