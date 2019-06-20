<?php
namespace App\Exalert;

use App\Exalert\Command;
use App\Exalert\Tsession;
//use App\Exalert\ExTask;

class Wtd extends Command
{
    public function execute($message, $args)        
    {     
        $this->response->text = 'nothing done';
        return $this;
    }

    public function getTask($message, $args)
    {
        $taskList = 'task list';;
        if (isset($args[0]) ) {
            $this-> response->text = 'get row num ' . $args[0];
        /*    $taskList = ExTask::where('user_id', $messgae->from->id)
                                ->where('num', (int)$args[0])
                                ->get();
         */
        } else {
            $this->response->text = 'get all tasks';
        /*    $taskList = ExTask::where('user_id', $message->from->id)
                                ->orderBy('num')
                                ->get();
         */
        }
        $this->response->text = view('exalert.task_list', [$taskList])->render();
        return $this;
    }

    public function newTask($message, $args) 
    {
        //need min 2 items in args
        $this->response->text = 'new task';
        return $this;
    }

    public function editTask($message, $args)
    {
        $this->response->text = 'edit task';
        return $this;
    }

    public function delTask($message, $args)
    {
        $this->response->text = 'delete task';
        return $this;
    }

    public function start($Message, $args)
    {
        $this->response->text = 'start watching';
        return $this;
    }

    public function stop($messgae, $args)
    {
        $this->response->text = 'stop watching';
        return $this;
    }

    /*wtd calls method getTask  return rows
    * wtd [num]  calls method getTask(num) return row 
    * wtd_new  [btc usd minvalue maxvalue] calls  method newTask return  row
    * !!!!  How fixed selected row 
    * wtd_set [min] 200  calls method editTask(param, value); 
    * wtd_set [max] 700   
    * wtd_del {num} return 'pair removed'
    * wtd_start {num}
    * wtd_stop {num}
    */
}
