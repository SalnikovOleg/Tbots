<?php

namespace App\Exalert\Commands;

use App\Exalert\Exmo;
use App\Exalert\Commands\Command;

class Cur extends Command
{
    const BUTTONS= [
       ['text' =>'BTC-USD', 'callback_data'=>'/cur btc usd'], 
       ['text'=>'BTC-UAH',  'callback_data'=>'/cur btc uah'],
       ['text'=>'Exmo', 'url'=>'https://exmo.com/'],
   ];

    public function execute($args)
    {
        $pair = self::getPair($args);   
        if (!$pair) {
            $this->response->text = 'Select pair';
            $this->response->inline_keyboard = self::BUTTONS;
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

    public static function getPair($args)
    {
        if (count($args)==0) return '';

        $args[0] = str_replace('-','_',$args[0]);
        
        if (strpos($args[0],'_')===false && count($args)>1) { 
           $result =  strtoupper($args[0]) . '_' . strtoupper($args[1]);
        }  elseif (strpos($args[0],'_')===false) {
            $result = '';
        } else {
            $result = strtoupper($args[0]);
        }
        return $result;
    }

}
