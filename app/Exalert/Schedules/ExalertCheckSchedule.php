<?php
namespace App\Exalert\Schedules;

use App\Exalert\Exmo;
use App\Exalert\Models\ExTask;
use App\Exalert\Models\Exchange;

class ExalertCheckSchedule
{
    /**
     * sensitivity of changes by pair 
     * TODO store in db
     */ 
    private $sens = ['BTC_USD'=>10, 'BTC_UAH'=>260];
    
    /**
     * artisan command handler 
     */ 
    public function handle()
    {       
        $tasks = ExTask::where('active', 1)->get();
        if ($tasks->isEmpty() ) {
            return 'No active tasks';
        }
        $pairs = array_unique($tasks->map(function($task){
            return $task->pair;
        })->all());

        $currentExchanges = $this->getCurrencyExchange($pairs);
        $prevExchanges = Exchange::find($pairs);

        $changes = $this->checkChanges($prevExchanges, $currentExchanges);
      
        //$response = $this->processing($changes, $tasks);
      
        $response = 'task count : ' . count($tasks) . '; pairs : ' . implode(',',$pairs) . ' chanhes ' . count($changes);
        return $response;
    }
    /**
     * get current exchange statistics by selected pairs
     * @param array $pairs
     * @return array
     */ 
    private function getCurrencyExchange($pairs)
    {       
        $currencies = json_decode('{"BTC_USD":{"buy":"12665.00000012","sell":"12700"},"BTC_UAH":{"buy":"313300.00000004","sell":"317347.466173"}}', true);
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


    private function checkChanges($prevExchanges, $currentExchanges)
    {
        $changes = collect([]);
        foreach ($currentExchanges as $pair => $row) {
            $index = $prevExchanges->search(function($item, $key) use($pair) {
                return $item['pair'] == $pair;
            }); 
            if (!$index) {
                $prevItem = Exchange::create([
                    'id' => $pair, 
                    'buy' => $row['buy'], 
                    'sell' => $row['sell'], 
                    'diff' => 0
                ]);
                $changes->push($prevItem);
            } else {
                $prevItem = $prevExchange->get($index);
                $diff = $this->getDiff($row, $prevItem->toArray());
                $direction = $this->getDirection($prevItem->diff, $diff);
                $prevItem->fill([
                    'id' => $pair,
                    'buy' => $row['buy'],
                    'sell' => $row['sell'],
                    'diff' => $diff,
                ]);
                //TODO changes direction 
                if (abs($prevItem->diff) > $this->sens[$pair]) { 
                    $prevItem->save();
                    $changes->push($prevItem);
                }
            }
        }
        return $changes;
    }
  
    private function getDiff($curr, $prev)
    {
        $buyDiff = $prev['buy'] - $curr['buy'];
        $sellDiff = $prev['sell'] - $curr['sell'];
        return abs($buyDiff) > abs($sellDiff) ? $buyDiff : $sellDiff;
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
