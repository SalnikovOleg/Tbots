<?php
/*
 * webhook url which resive all commands
 * 1.detect command
 * 1.1. check uncomplete command
 * 1.2. send qery for arguments
 * 2.if it is possible to respone :
 * 2.1.do command
 * 2.3. send response
 * 3.if need arguments :
 * 3.1. save tcommands
 * 3.2. send qery for arguments
 *
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram;
use App\Models\Exmo;

class Exalert extends Controller
{	
    public function getUpdates() {
        $updates = Telegram::getUpdates();
	if (count($updates['result'])>0) {
	    $lastMessage = $updates['result'][count($updates['result'])-1];
	   return $this->update($lastMessage);
	}
	return json_encode($updates);
    }

    public function sendMessage($mess='test message') {
	$message =[
	    'chat_id' => 334780940,
	    'text' => $mess,
	];
	Telegram::sendMessage($message);
	return json_encode($message);
    }
    
    public function exmo() 
    {
	$exmo = new Exmo();
	print_r($exmo->currency());
	
    }

    /*
     * emulate webhook
     * late to remove post argument and use request
     */
    public function update($post)
    {
	$data = $post;

    }

}
