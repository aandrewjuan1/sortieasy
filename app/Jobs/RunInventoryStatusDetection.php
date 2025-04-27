<?php

// app/Jobs/RunInventoryStatusDetection.php

namespace App\Jobs;

use App\Services\InventoryStatusService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RunInventoryStatusDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Set the job's timeout if you expect long-running processes
    public $timeout = 300; // 5 minutes for example


    public function handle(InventoryStatusService $inventoryStatusService): void
    {
        try {
            Log::info('🚀 Starting inventory status detection from job...');

            // Call the method in the service to run the detection
            $inventoryStatusService->runInventoryStatusDetection();

            Log::info('✅ Inventory status detection completed.');
        } catch (\Throwable $e) {  // Catching a more general exception type for better error tracking
            Log::error('❌ Error in inventory status detection: ' . $e->getMessage());
            // Optionally, you can dispatch the job again in case of failure, or handle retries
            // $this->release(30); // Release the job to retry in 30 seconds, for example.
        }
    }
}
