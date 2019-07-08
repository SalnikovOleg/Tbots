<?php
/* */
namespace App\Exalert\Commands;

use App\Exalert\Models\Tsession;

class CommandsMngr
{
    private $mainKeyboard = [
        ['text' => '/cur'],
        ['text' => '/wtd'],
        ['text' => '/help'],
        ['text' => '/end']
    ];
    const LIST = [
        'start' => ['method'=>'commandStart'], 
        'help' => ['method'=>'commandHelp'], 
        'cur' => ['class'=>'App\Exalert\Commands\Cur', 'method'=>'execute'],
        'wtd' => ['class'=>'App\Exalert\Commands\Wtd', 'method'=>'getTask'],
        'wtd_new'=> ['class'=>'App\Exalert\Commands\Wtd', 'method'=>'newTask'],
        'wtd_del' => ['class'=>'App\Exalert\Commands\Wtd', 'method'=>'delTask'],
        'end' => ['method' => 'commandEnd'],
        'wtd_start' => ['class'=>'App\Exalert\Commands\Wtd', 'method'=>'start'],
        'wtd_stop' => ['class'=>'App\Exalert\Commands\Wtd', 'method'=>'stop'],
    ];

    private $response = null;
    private $command = null;
    private $message = null;
    private $tSession = null;

    public function __construct($message) 
    {
        $this->command = (object)[
            'name'=>'',
            'className'=>'', 
            'methodName'=>'', 
            'args'=>[]
        ];
        $this->response =(object)[
            'text'=>'', 
            'inline_keyboard'=>[], 
            'status'=>false,
            'keyboard'=>[],
        ];

        $this->message = $message;
        $this->executable = $this->extractCommand($message['text']);
        $this->tSession = $this->checkTSession($message);

        if ($this->command->name == 'end') 
            return;

            //resived command no equal current command
        if ($this->executable && $this->tSession->command ) {
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
        if (!$this->response->text) 
            return false;

	    return $this->response;
    }
    
    private function wrongCommand() 
    {
       $tmpl = 'Current is /%s. Send /%s end to finish it';
       return  sprintf($tmpl,
                $this->tSession->command, 
                $this->tSession->command
            ); 
    }

    private function extractCommand($text)
    {
        $text= preg_replace("/\s{2,}/"," ",$text);
        $chunk = explode(' ', $text);
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
            'user_id'=> $message['user_id'],
            'chat_id'=> $message['chat_id'],
            'done'=> false
        ]);
        return $tSession;
    }

    private function restoreCommand($tSession, $message) 
    {
        $name =  $this->tSession->command;
        $this->command->name = $name;
        $this->command->className = isset(self::LIST[$name]['class']) ?
                self::LIST[$name]['class'] : 
                'this';
        $this->command->methodName = $this->tSession->method ? 
                $this->tSession->method : 
                (isset(self::LIST[$name]['method']) ?
                 self::LIST[$name]['method'] : 
                 'execute');
        
        $chunk = explode(' ',  preg_replace("/\s{2,}/", " ", $message['text']));
        $this->command->args = $chunk;
        return true;
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
            $this->{$this->command->methodName}($this->command->args);
         } else {
            $this->wrongMethod();
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
        $command = $app->make($this->command->className,[
            'message'=>$this->message,
            'tsession'=>$this->tSession]);
        $result = $app->call([$command, $this->command->methodName],['args'=>$this->command->args]);
        $this->response = $result->getResponse();
        return $result->getStatus();
    }

    private function commandStart($args)
    {
        $this->response->keyboard = $this->mainKeyboard;
        $this->response->text = view('exalert.start')->render();
        $this->response->status = true;
        return true;
    }

    private function commandHelp($args)
    {
        $this->response->text = view('exalert.help')->render();
        $this->response->status = true;
        return true;
    }

    private function commandEnd()
    {
        if ($this->tSession->command) {
            $this->tSession->done = true;
            $this->tSession->save();
            $this->response->text =  $this->tSession->command . ' finished';
            $this->response->status = true;
        } else {
            $this->response->text =  'No method to finish';
            $this->response->status = false;
        }
        return $this->response->status;
    }

    private function wrongMethod()
    {
        $this->response->text = 'Method ' . $this->command->methodName 
            . ' does not exist';
        $this->response->status = false;
        return false;
    }
}

