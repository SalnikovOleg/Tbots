<?php
namespace App\Exalert\Schedules;

use App\Exalert\Exmo;
use App\Exalert\Models\ExTask;
use App\Exalert\Models\Exchange;

class CheckCurrencyShedule
{
    /**
    *kactive tesks collection of ExTask
    * @var ExtTask
    */
    private $tasks;

    /**
     * @var array 
     * list of changes exchange
     */
    private $changes;

    /**
     * artisan command handler 
     */ 
    public function handle()
    {       
        $this->tasks = ExTask::where('active', 1)->get();
        if (empty($this->tasks) || count($this->tasks)==0) {
            return 'No active tasks';
        }
        $pairs = array_unique($this->tasks->map(function($task){
            return $task->pair;
        })->all());

        $currentExchange = $this->getCurrencyExchange($pairs);
        $prevExchange = Exchange::find($pairs);

        $this->changes = $this->checkChanges($prevExchange, $currentExchange);
      
        $response = $this->processing();

        $response = 'task count : ' . count($this->tasks) . '; pairs : ' . implode(',',$pairs);
        return $response;
    }

    /**
     * get current exchange statistics by selected pairs
     * @param array $pairs
     * @return array
     */ 
    private function getCurrencyExchange($pairs)
    {       
        $currencies = json_decode('{"BTC_USD":{"buy":"12665.00000012","sell":"12700"},"BTC_UAH":{"buy":"313300.00000004","sell":"317347.466173"}}');
        return $currencies;
     /*   $currencies = Exmo::getPairsExchange($this->pairs);
          return  array_map( 
            function($item) {
                return ['buy'=>$item['buy_price'], 'sell' => $item['sell_price']];
            }, 
            $currencies
        );
      */
    }


    private function checkChanges($prevExchange, $currentExchange)
    {

    }

    /**
     * check changes, compare given min max values and markctask if need alert
     * return string to console report
     */ 
    private function processing()
    {
        foreach($this->tasks as $task) {

        }    
    }

}
