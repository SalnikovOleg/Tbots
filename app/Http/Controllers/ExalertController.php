<?php
/*
 * webhook url which resive all commands
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram;
//use App\Models\ExalertCommands;
use GuzzleHttp;

class ExalertController extends Controller
{	

    public function getUpdates() 
    {
        $updates = Telegram::getUpdates();
        return json_encode($updates);
        
	    if (count($updates['result'])>0) {
            $lastMessage = $updates['result'][count($updates['result'])-1];
            return json_encode($lastMessage);
	       // return $this->webhookHandler($lastMessage);
	    }
	    return json_encode($updates);
    }

    /*
    * emulate webhook
    * late to remove post argument and use request
    */
    public function webhookHandler()
    {
        $update = Telegram::commandsHandler(true);
        return $update;
             //$data = json_encode($request->all()); 
            // file_put_contents('/data/data/com.termux/files/home/proj/log.txt', $data);
           
           // return $data;
    }

    /*j
     *test get update handler
    */
    public function updateHandler($update)
    {
	    //$update = Telegram::getWebhookUpdate();
        $message = $update->getMessage();
        $command = new ExalertCommand($message->text);
        if ($command->executable) {
            $response = $command->execute();
        } else {
            $response = 'not valide command';
        }
        $answer = [
            'chat_id' => $message->chat_id,
            'text' => $response 
        ];
        Telegram::sendMessage($answer);
    }

}
