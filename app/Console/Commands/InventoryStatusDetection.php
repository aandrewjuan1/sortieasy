<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryStatusService;

class InventoryStatusDetection extends Command
{
    protected $inventoryStatusService;

    protected $signature = 'detect:inventory-status';
    protected $description = 'Detect and update inventory status for all products';

    public function __construct(InventoryStatusService $inventoryStatusService)
    {
        parent::__construct();
        $this->inventoryStatusService = $inventoryStatusService;
    }

    public function handle()
    {
        $this->info('🚀 Starting Inventory Status Detection...');

        $sales = DB::table('sales')
            ->select('product_id', 'quantity', 'sale_date')
            ->get();

        $products = DB::table('products')
            ->select('id', 'name', 'quantity_in_stock')
            ->get()
            ->keyBy('id');

        if ($sales->isEmpty() || $products->isEmpty()) {
            $this->error('❌ No sales or products data found.');
            return;
        }

        $today = Carbon::today();
        $salesAgg = $sales->groupBy('product_id')->map(function ($salesGroup) use ($today) {
            return $this->inventoryStatusService->aggregateSalesData($salesGroup, $today);
        });

        $updates = [];

        foreach ($products as $product) {
            $stats = $salesAgg->get($product->id, [
                'total_quantity_sold' => 0,
                'days_since_last_sale' => 9999,
            ]);

            $this->info("🔍 Product {$product->id}: Total Sold: {$stats['total_quantity_sold']}, Days Since Last Sale: {$stats['days_since_last_sale']}");

            $status = $this->inventoryStatusService->determineStatus($product, $stats);

            $updates[] = [
                'id' => $product->id,
                'inventory_status' => $status->value,
            ];
        }

        foreach ($updates as $update) {
            DB::table('products')
                ->where('id', $update['id'])
                ->update(['inventory_status' => $update['inventory_status']]);
        }

        $this->info('✅ Inventory Status Detection Completed! Updated '.count($updates).' products.');
    }
}
