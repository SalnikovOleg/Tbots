<?php

namespace App\Exalert;

use App\Models\Exmo;

class Cur
{
    private $content = '';
    private $status = false;

    public function getContent()//TODO define it in base model
    {
        return $this->content;
    }

    public function getStatus()//TODO define it in base model
    {
        return $this->status;
    }

    public function execute($pair)
    {
     /* $exmo = new Exmo();
        $currency = $exmo->currency($pair);
        print_r($currency);
        $str = 'dd';//'buy = ' . $currency[''] . '/ sell = ' . $currency[""]; 
        return $str;*/

        $this->content = ' This is model Cur';
        $this->status = true;
        return $this;
    }
}
