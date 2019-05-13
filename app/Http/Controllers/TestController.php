<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp;
class TestController extends Controller
{
     public function testWebhook()
    {
        $post= '[{"update_id":208026289,"message":{"message_id":450,"from":{"id":809835134,"is_bot":false,"first_name":"oleg","last_name":"S","language_code":"ru"},"chat":{"id":809835134,"first_name":"oleg","last_name":"S","type":"private"},"date":1557728655,"text":"\/help","entities":[{"offset":0,"length":5,"type":"bot_command"}]}}]';
       // print_r(json_decode($post));
       // die();
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST',
                'http://localhost:8000/exalert/webhook/', [
                    'headers' => [
                        'XSRF_TOKEN'=>csrf_token()
                    ],
                    'form_params'=>[
                        '_token'=>csrf_token(),
                        'page'=>1
                    ],
                ]
            );
         var_dump($response->getBody()->getContents());
       // var_dump($response);
    }
}
