<?php
/*
 * webhook url which resive all commands
 */
namespace App\Http\Controllers;

use Telegram;
use App\Exalert\Commands;

class ExalertController extends Controller
{	

    public function getUpdates() 
    {
        $updates = Telegram::getUpdates();
       // return json_encode($updates);
        
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
    
        $command = new Commands($message->text);
        if ($command->isExecutable() && $command->execute()) {
            $response = $command->getResponse();
        } else {
            $response = 'not valide command';
        }
        Telegram::sendMessage([
            'chat_id' => $message->chat->id,
            'text' => $response,
            'parse_mode' => 'HTML'
        ]);
    }

        
}
