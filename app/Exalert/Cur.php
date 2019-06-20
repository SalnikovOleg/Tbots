<?php

namespace App\Exalert;

use App\Exalert\Exmo;
use App\Exalert\Command;

class Cur extends Command
{
    const BUTTONS= [
       ['text' =>'BTC-USD', 'callback_data'=>'/cur btc usd'], 
       ['text'=>'BTC-UAH',  'callback_data'=>'/cur btc uah'],
       ['text'=>'Exmo', 'url'=>'https://exmo.com/'],
   ];

    public function execute($args)
    {
        $pair = $this->getPair($args);   
        if (!$pair) {
            $this->response->text = 'Return Buttons';
            $this->response->buttons = self::BUTTONS[0];
        } else {

            $exmo = new Exmo();
            $currency = $exmo->currency($pair);
            if (count($currency)>0) { 
                $this->response->text = view('exalert.cur', [
                     'pair'=>$pair,
                     'cur'=>$currency
                ])->render();
            } else {
                $this->response->text = $pair . ' pair is not exists';
            }
        }
        
        $this->response->status = true;
        return $this;
    }

    private function getPair($args)
    {
        if (count($args)==0) return '';

        if(count($args) > 1) { 
           return  strtoupper($args[0]) . '_' . strtoupper($args[1]);
        } 

        $args[0] = str_replace('-','_',$args[0]);
        
        if (strpos($args[0],'_')===false) return '';

        return strtoupper($args[0]);
    }

}
