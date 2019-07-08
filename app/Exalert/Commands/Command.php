<?php

namespace App\Exalert\Commands;


class Command
{
    protected $response = null; 
    protected $userId = 0;
    protected $tSession = null;

    public function __construct($message,$tsession)
    {
        $this->userId = $message['user_id'];
        $this->tSession = $tsession;
        $this->response  =(object)[
            'text'=>'', 
            'inline_keyboard'=>[], 
            'status'=>false,
            'keyboard'=>[],
        ];
    }
    public function getResponse()
    {
        return $this->response;
    }

    public function getStatus()
    {
        return $this->response->status;
    }

    protected function setResponse($text, $status=true)
    {
        $this->response->text = $text;
        $this->response->status = $status;
        return $this;
    }

}
