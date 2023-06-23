<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


class APIResponseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'APIResponseCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command will return JSON response including Email, InTime, OutTime, Total_Minutes, IPAddress';

    /**
     * Execute the console command.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function handle()
    {
        return Http::get('https://champagne-bandicoot-hem.cyclic.app/api/data');
    }
}
