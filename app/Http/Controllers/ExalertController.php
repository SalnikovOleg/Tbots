<?php
/*
 * webhook url which resive all commands
 */
namespace App\Http\Controllers;

use Telegram;
use App\Exalert\CommandsMngr;
use App\Exalerta\TelegramKeyboard as Keyboard;
//use Telegram\Bot\Keyboard\Keyboard;

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
     *test get update handler
    */
    public function webhookHandler($update)
    {
	    //$update = Telegram::getWebhookUpdate();
        $message = $update->getMessage();
        //TODO checkup callback_response and define $messgae
        //chat_id, user_id, text
        // print_r($message);
        $command = new CommandsMngr($message);

        if ($command->isExecutable() ) {
            $command->execute();
        }       
        $response = $command->getResponse();
        if($response) {
            $message =['chat_id' => $message->chat->id, 
                'text' => $response->text,
               // 'parse_mode'=>'HTML'
            ];
            if (count($response->buttons)>0) {
                $message['reply_markup'] =  Keyboard::make()
                        ->inline()
                        ->row(Keyboard::inlineButton($response->buttons));  
            }
       // var_dump($message['reply_markup']);
            //:wq
            //die();
            Telegram::sendMessage($message);
        } else { 
            echo 'response is empty<br>';
            var_dump($message->text);
        }
    }    

}
