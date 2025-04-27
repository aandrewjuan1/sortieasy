<?php

namespace App\Console\Commands;

use App\Jobs\RunForecasts;
use Illuminate\Console\Command;

class DemandForecast extends Command
{
    protected $signature = 'forecast:generate';
    protected $description = 'Run the demand forecasting Python script';

    public function handle()
    {
        RunForecasts::dispatch();

        $this->info('🚀 Demand forecasts dispatched to queue.');
    }
}
