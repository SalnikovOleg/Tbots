<?php

namespace App\Exalert;


class Command
{
    protected $response = null; 

    public function __construct()
    {
        $this->response  =(object)['text'=>'', 'buttons'=>[], 'status'=>false];
    }
    public function getResponse()
    {
        return $this->response;
    }

    public function getStatus()
    {
        return $this->response->status;
    }

}
