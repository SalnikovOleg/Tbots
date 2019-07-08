<?php
/*
 * webhook url which resive all commands
 */
namespace App\Http\Controllers;

use Telegram;
use App\Exalert\Commands\CommandsMngr;
use App\TelegramKeyboard as Keyboard;

class ExalertController extends Controller
{	

    public function getUpdates() 
    {
        $updates = Telegram::getUpdates();
      //  return json_encode($updates);
        
	    if (count($updates)>0) {
            $lastMessage = $updates[count($updates)-1];
            //return json_encode($lastMessage);
	        return $this->webhookHandler($lastMessage);
	    }
	    return json_encode($updates);
    }

    /*
     * get update handler
    */ 
    public function webhookHandler($update)
    {
        //$update = Telegram::getWebhookUpdate();
        $message = $this->getMessage($update);
        $command = new CommandsMngr($message);
                                    
        if ($command->isExecutable() ) {
            $command->execute();
        }       

        $response = $command->getResponse();
     
        if($response) {
            $message['text'] = $response->text;
            $message['reply_markup'] = $this->getReplyMarkup($response);
         //  print_r($message);
         //  die();
            Telegram::sendMessage($message);

        } else { 
            echo 'response is empty<br>';
            var_dump($message['text']);
        }
    }    

    private function getMessage($update)
    {
        $message=[
            'chat_id'=>0, 
            'user_id'=>0, 
            'text'=>'',
            'parse_mode'=>'HTML'
        ];

        if ($update->isType('callback_query')) {
            $message['chat_id'] = $update->callbackQuery->from->id;
            $message['user_id'] = $update->callbackQuery->from->id;
            $message['text'] = $update->callbackQuery->data;
        } else {
            $mess = $update->getMessage();
            $message['chat_id'] = $mess->chat->id;
            $message['user_id'] = $mess->from->id;
            $message['text'] = $mess->text;
        }
        return $message;
    }

    private function getReplyMarkup($response)
    {
        if (count($response->inline_keyboard)>0) {
            return  Keyboard::make()
                        ->inline()
                        ->rows($response->inline_keyboard);  
        } elseif(count($response->keyboard)>0) {
            return Keyboard::make()->rows($response->keyboard);
        }
        return false;
    }
}
