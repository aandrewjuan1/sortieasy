<?php

// app/Jobs/RunInventoryStatusDetection.php

namespace App\Jobs;

use App\Services\ForecastService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RunForecasts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Set the job's timeout if you expect long-running processes
    public $timeout = 300; // 5 minutes for example

    public function handle(ForecastService $forecastService): void
    {
        try {
            Log::info('🚀 Starting demand forecast from job...');

            $forecastService->runForecasts();
            Log::info('✅ Demand forecasts completed.');
        } catch (\Throwable $e) {  // Catching a more general exception type for better error tracking
            Log::error('❌ Error: ' . $e->getMessage());
        }
    }




}
