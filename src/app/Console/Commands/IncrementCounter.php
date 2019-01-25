<?php

namespace App\Console\Commands;

use App\Jobs\IncrementCounter as IncrementCounterJob;
use Cache;
use Illuminate\Console\Command;

class IncrementCounter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'counter:increment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asynchronously increment the counter';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Cache::increment('increment-requests');

        IncrementCounterJob::dispatch();
    }
}
