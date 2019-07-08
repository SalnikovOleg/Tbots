<?php

namespace App;
 
use Telegram\Bot\Keyboard\Keyboard;

class TelegramKeyboard extends Keyboard
{

    /**
     * add row of nuttons from array of params
     * $btnArray = [
     *       ['text'=>'some text', 'callback_data'=>'some_data'],
     *       ['text'=>'some text', 'url'=>'some external url']...
     *  ]
     */
    public function rows($btnArray): Keyboard
    {
        if($this->isInlineKeyboard()) {
            $this->addInlineRows($btnArray);
        } else {
            $this->addRows($btnArray); 
        }
        return $this;
    }

    /**
     * add buttons to inline keyboards
     * TODO two level array
     */ 
    private function addInlineRows($btnArray)
    {
        $buttons = array_map(function($btn) {
          return self::inlineButton($btn);
        }, $btnArray);
        $this->items['inline_keyboard'][]=$buttons;

    }

    private function addRows($btnArray)
    {       
        $buttons = array_map(function($btn) {
          return $btn['text'];
        }, $btnArray);
        $this->items['keyboard'][]=$buttons;
        $this->items['resize_keyboard'] = true;
    }
}
