<?php
namespace App\Exalert;
use GuzzleHttp;

class Exmo {
    private $url = 'https://api.exmo.com/v1/ticker/';

    private function ticker() 
    {
	$client = new \GuzzleHttp\Client();
	$request=$client->get($this->url);
	$data = $request->getBody()->getContents();
	return json_decode($data);
    }

    public function currency($pair='BTC_USD')
    {
	$currencies = $this->ticker();
	return $currencies->{$pair};
    }
}
