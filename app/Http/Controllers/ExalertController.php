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
       // $update = $this->telegram->commandsHandler(true);
        //  return $update;
        return '$$$$$$$$$$$_________#######################---------===';
    }

    /*
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

/*   public function testWebhook()
    {
        $post= '[{"update_id":208026289,"message":{"message_id":450,"from":{"id":809835134,"is_bot":false,"first_name":"oleg","last_name":"S","language_code":"ru"},"chat":{"id":809835134,"first_name":"oleg","last_name":"S","type":"private"},"date":1557728655,"text":"\/help","entities":[{"offset":0,"length":5,"type":"bot_command"}]}}]';
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
                'http://localhost:8000/exalert/webhook/',
                ['json' => json_decode($post)]
            );
        print_r($response->getBody());
}*/
}
