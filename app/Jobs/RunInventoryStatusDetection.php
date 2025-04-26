<?php

namespace App\Jobs;

use App\Services\InventoryStatusService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class RunInventoryStatusDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $today;

    public function __construct()
    {
        // You can pass parameters if needed later
        $this->today = Carbon::today();
    }

    public function handle(InventoryStatusService $inventoryStatusService): void
    {
        logger('🚀 Starting Inventory Status Detection Job...');

        $sales = DB::table('sales')
            ->select('product_id', 'quantity', 'sale_date')
            ->get();

        $products = DB::table('products')
            ->select('id', 'name', 'quantity_in_stock')
            ->get()
            ->keyBy('id');

        if ($sales->isEmpty() || $products->isEmpty()) {
            logger('❌ No sales or products data found.');
            return;
        }

        // Aggregate sales data
        $salesAgg = $sales->groupBy('product_id')->map(function ($salesGroup) use ($inventoryStatusService) {
            return $inventoryStatusService->aggregateSalesData($salesGroup, $this->today);
        });

        $updates = [];

        foreach ($products as $product) {
            $stats = $salesAgg->get($product->id, [
                'total_quantity_sold' => 0,
                'days_since_last_sale' => 9999,
            ]);

            $status = $inventoryStatusService->determineStatus($product, $stats);

            logger("🔍 Product {$product->id}: Status determined as {$status->value}");

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

        logger('✅ Inventory Status Detection Completed. Updated ' . count($updates) . ' products.');
    }
}
