<?php
namespace App\Exalert\Commands;

use App\Exalert\Commands\Command;
use App\Exalert\Models\Tsession;
use App\Exalert\Commands\Cur;
use App\Exalert\Models\ExTask;

class Wtd extends Command
{
    private $taskList = [];

    public function execute($args)        
    {     
        return $this->setResponse('nothing to do' , false);
    }

    /**
     * return one or all of  user's tasks 
     * @param array $args 
     * $args can be  [1] or [2].. etc 
     * or  ['btc_usd'] or ['btc', 'usd'] 
     */
    public function getTask($args)
    {        
        if (count($args) == 0) {
            $args=['all'];
        }
        $params = $this->getQueryCriteria($args);
        if (empty($params)) {
            return $this->setResponse('Bad params' , false);
        }

        $task = ExTask::where($params)->get();
        if (empty($task) || count($task)==0) {
            return $this->setResponse('Not found', false);
        } elseif (count($task) == 1) {
            $this->createSession($task[0]->id);
        }
        
        $text = view('exalert.task_list', ['list'=>$task])->render();
        return $this->setResponse($text);
    }    

    public function newTask($args) 
    { 
        $pair = Cur::getPair($args);
        if (!$pair) {
          return  $this->setResponse('No pair', false);
        }           
        $task = ExTask::firstOrCreate([
            'user_id' => $this->userId,
            'pair' => $pair
        ]);
        if ($task->number == 0) {
            $task->number = ExTask::where('user_id', $this->userId)->max('number') + 1;
            $task->save();
        }
        if (!$this->newTaskComplete($task, $args)) {  
            $this->createSession($task->id, 'need min max value');
        }
        $text = view('exalert.task_list', ['list'=>[$task]])->render();
        return $this->setResponse($text);
    }
    
    private function createSession($id, $comment='')
    {     
            $session = Tsession::create([
                'user_id' => $this->userId,
                'chat_id' => $this->userId,
                'command' => 'wtd',
                'method' => 'update',
                'done' => false,
                'value' => $id,
                'comment' => $comment
            ]);
    }

    /**
     * try to complete update new task by min max values
     *
     */ 
    private function newTaskComplete($task, $args)
    {
        if (!empty($task->min) || !empty($task->max)) {
            return true;
        }
        $i = count($args) - 2;
        if ( isset($args[$i]) && (int)$args[$i]>0) {
            $task->min = (int)$args[$i];
        }
        $i = count($args) - 1;
        if( (isset($args[$i]) && (int)$args[$i]>0)) {         
            $task->max = (int)$args[$i];
        }
        if (!empty($task->min) || !empty($task->max)) {
            $task->save();
            return true;
        }
        return false;
    }

    /**
     * update task min max values
     */
    public function update($args)
    {   
        if ((int)$this->tSession->value == 0) 
            return $this->setResponse('Not found', false);
    
        $task = ExTask::find($this->tSession->value);
        if (empty($task))
            return $this->setResponse('Not found', false);

        $valideArgs = false;
        if (count($args) == 2) {
            if (is_numeric($args[0]) && is_numeric($args[1])) {
                $v0 = (float)$args[0];
                $v1 = (float)$args[1];
                $task->min = $v0 < $v1 ? $v0 : $v1;
                $task->max = $v1 > $v0 ? $v1 : $v0;
                $valideArgs = true;
            } elseif(is_numeric($args[1]) && in_array($args[0], ['min','max'])) {
                $task->{$args[0]} = (float)$args[1];
                $valideArgs = true;
            }
        } elseif (count($args) == 4 && 
            is_numeric($args[1]) &&
            is_numeric($args[3]) &&
            in_array($args[0],['min','max']) &&
            in_array($args[1],['min','max'])) {       
                $task->{$args[0]} = (float)$args[1];
                $task->{$args[2]} = (float)$args[3];
                $valideArgs = true;
        }
        if ($valideArgs) {
            $task->save();
            $text = view('exalert.task_list', ['list'=>[$task]])->render();
            return $this->setResponse($text);
        } else {
            return $this->setResponse('Bad arguments', false);
        }
    }

    public function delTask($args)
    {
        $task = $this->searchByArgs($args);
        if ($task) {
            $task->delete();  
            $this->setResponse('Pair ' . $task->pair . ' deleted');
        }
        return $this;
    }

    public function start ($args)
    {
        $task = $this->searchByArgs($args);
        if ($task) {
            $task->active = true;
            $task->save();
            $text = view('exalert.task_list', ['list'=>[$task]])->render();
            $this->setResponse($text);
        }
        return $this;
    }

    public function stop($args)
    {
        $task = $this->searchByArgs($args);
        if ($task) {
            $task->active = false;
            $task->save();
            $text = view('exalert.task_list', ['list'=>[$task]])->render();
            $this->setResponse($text);
        }
        return $this;
    }

    private function getQueryCriteria($args)
    {
        if (isset($args[0]) && $args[0] == 'all') {
            return ['user_id'=>$this->userId];
        }
        $number = isset($args[0]) ? (int)$args[0] : 0;
        $pair = Cur::getPair($args);
        if (!$pair && $number) {
            return ['user_id'=>$this->userId, 'number'=>$number];
        }
        if (!$number && $pair) {
            return ['user_id'=>$this->userId, 'pair'=>$pair];
        }
        return null;
    }

    /**
     * search task by params from $args
     * @param array $args arguments for task searching 
     * @param array $values [field => value] 
     */ 
    private function searchByArgs($args)
    {
        $params = $this->getQueryCriteria($args);
        if (empty($params)) {
            $this->setResponse('Bad params', false);
            return false;
        }
        $task = ExTask::where($params)->first();
        if (empty($task)) {
            $this->setResponse('Not found', false);
            return false;
        }
        return $task;;
    }
    /*wtd calls method getTask  return rows
    * wtd [pair/num]  calls method getTask(num) return row 
    * wtd_new  [btc usd minvalue maxvalue] calls  method newTask return  row
    * if no min max next message expectedcthem
    * in this case will be call method update 
    * wtd_del {pair/num} return 'pair removed'
    * wtd_start {pair/num}
    * wtd_stop {pair/num}
    * after delete renumerate task for user
    */
}
