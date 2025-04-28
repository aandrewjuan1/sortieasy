<?php

namespace App\Console\Commands;

use App\Jobs\RunAnomalyDetection;
use Illuminate\Console\Command;

class AnomalyDetection extends Command
{
    protected $signature = 'anomaly:detect';
    protected $description = 'Run the anomaly detection Python script for inventory transactions';

    public function handle()
    {
        RunAnomalyDetection::dispatch();

        $this->info('🚀 Anomaly detection dispatched to queue.');
    }
}
