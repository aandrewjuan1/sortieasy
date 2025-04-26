<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\RunInventoryStatusDetection;

class InventoryStatusDetection extends Command
{
    protected $signature = 'detect:inventory-status';
    protected $description = 'Detect and update inventory status for all products';

    public function handle()
    {
        RunInventoryStatusDetection::dispatch();

        $this->info('🚀 Inventory Status Detection dispatched to queue.');
    }
}
