<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessAPIResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:process-response';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for storing the daily basis attendance status of the employees.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        app('App\Http\Controllers\ApiResponseController')->APIResponseUserTimeLocation();
    }
}
