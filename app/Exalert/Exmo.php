<?php
namespace App\Exalert;
use GuzzleHttp;

class Exmo {
    const URL = 'https://api.exmo.com/v1/ticker/';

    private static function ticker() 
    {
	    $client = new \GuzzleHttp\Client();
	    $request=$client->get(self::URL);
	    $data = $request->getBody()->getContents();
	    return json_decode($data, true);
    }

    public function currency($pair='BTC_USD')
    {
        $currencies = self::ticker();
        if(isset($currencies[$pair])) {
            return $currencies[$pair];
        } else {
            return array();
        }
    }
    
    public static function getPairsExchange($pairs)
    {
        $currencies = self::ticker();
        $result = array_filter($currencies,
            function($key) use($pairs){
                return in_array($key, $pairs);
            },
            ARRAY_FILTER_USE_KEY
        );
        return $result;
    }
}
