<?php

// app/Jobs/RunInventoryStatusDetection.php

namespace App\Jobs;

use App\Services\AnomalyDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RunAnomalyDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Set the job's timeout if you expect long-running processes
    public $timeout = 300; // 5 minutes for example

    public function handle(AnomalyDetectionService $anomalyDetectionService): void
    {
        try {
            Log::info('🚀 Starting anomaly detection from job...');

            $anomalyDetectionService->runAnomalyDetection();
            Log::info(message: '✅ Anomaly detection completed.');
        } catch (\Throwable $e) {
            Log::error('❌ Error: ' . $e->getMessage());
        }
    }
}
