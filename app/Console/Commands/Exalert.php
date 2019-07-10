<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exalert\Schedules\CheckCurrencyShedule;

class Exalert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exalert:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check exmo currency';

    /**
     * @var CheckCurrencyShedule
     */ 
    private $schedule;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CheckCurrencyShedule $schedule)
    {
        parent::__construct();
        $this->schedule = $schedule;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->schedule->handle();

        $this->line($response);
    }
}
