<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp;
class TestController extends Controller
{
     public function testWebhook()
     {
      $data=json_decode('[{"update_id":208026290,"message":{"message_id":451,"from":{"id":809835134,"is_bot":false,"first_name":"oleg","last_name":"S","language_code":"ru"},"chat":{"id":809835134,"first_name":"oleg","last_name":"S","type":"private"},"date":1558073284,"text":"\/help","entities":[{"offset":0,"length":5,"type":"bot_command"}]}}]') ; 
       $client = new \GuzzleHttp\Client();
     
        $response = $client->post(
            'http://localhost:8000/webhook', 
            ['form_params'=>$data]
            );
         var_dump($response->getBody()->getContents());
        echo 'webhooktest';
     }

     public function index(Request $request)
     {
         print_r($request->post());
     }
}
