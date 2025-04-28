<?php

// app/Jobs/RunInventoryStatusDetection.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AnomalyDetectionService;
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
            Cache::forget('anomaly_results:page:1:per_page:10:sort:transaction_id:dir:DESC:search::product::anomalies_only:1');
        } catch (\Throwable $e) {
            Log::error('❌ Error: ' . $e->getMessage());
        }
    }
}
