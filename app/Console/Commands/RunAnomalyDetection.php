<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnomalyDetectionService;

class RunAnomalyDetection extends Command
{
    protected $signature = 'anomaly:detect';
    protected $description = 'Run the anomaly detection Python script for inventory transactions';

    protected $anomalyDetectionService;

    public function __construct(AnomalyDetectionService $anomalyDetectionService)
    {
        parent::__construct();

        $this->anomalyDetectionService = $anomalyDetectionService;
    }

    public function handle()
    {
        $this->info('🚀 Starting Anomaly Detection...');

        try {
            $this->anomalyDetectionService->runAnomalyDetection();
            $this->info('✅ Anomaly Detection Completed!');
        } catch (\Exception $e) {
            $this->error('❌ An error occurred: ' . $e->getMessage());
        }
    }
}
