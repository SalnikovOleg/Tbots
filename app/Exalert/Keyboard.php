<?php

namespace App\Exalert;
 
use Telegram\Bot\Keyboard\Keyboard;

class TelegramKeyboard extends Keyboard
{

    /**
     *
     */
    public function rows($btnArray): Keyboard
    {
       $btnArray.map(function($btn) {
           $this->row(self::inlineButton($btn));
       });
       return $this;
    }
}
