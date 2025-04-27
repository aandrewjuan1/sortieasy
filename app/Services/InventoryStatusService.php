<?php

// app/Services/InventoryStatusService.php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Enums\InventoryStatus;

class InventoryStatusService
{
    protected const SLOW_MOVING_DAYS_THRESHOLD = 30;  // Days since last sale to consider "Slow Moving"
    protected const OBSOLETE_DAYS_THRESHOLD = 90;     // Days since last sale to consider "Obsolete"



    public function determineStatus($product, $salesStats)
    {
        // No sales ever
        if ($salesStats['total_quantity_sold'] == 0) {
            return $product->quantity_in_stock > 0
                ? InventoryStatus::Obsolete
                : InventoryStatus::Normal;
        }

        // Days since last sale logic
        if ($salesStats['days_since_last_sale'] >= self::OBSOLETE_DAYS_THRESHOLD) {
            return InventoryStatus::Obsolete;
        }

        if ($salesStats['days_since_last_sale'] >= self::SLOW_MOVING_DAYS_THRESHOLD) {
            return InventoryStatus::SlowMoving;
        }

        return InventoryStatus::Normal;
    }

    public function aggregateSalesData($salesGroup, Carbon $today)
    {
        $totalQuantitySold = $salesGroup->sum('quantity');
        $lastSaleDate = $salesGroup->max('sale_date');

        $lastSaleDateParsed = Carbon::parse($lastSaleDate)->startOfDay();
        $todayStart = $today->copy()->startOfDay();

        $daysSinceLastSale = $lastSaleDateParsed->diffInDays($todayStart);

        return [
            'total_quantity_sold' => $totalQuantitySold,
            'days_since_last_sale' => $daysSinceLastSale,
        ];
    }

    /**
     * Run the full inventory status detection logic
     */
    public function runInventoryStatusDetection()
    {
        logger('🚀 Starting Inventory Status Detection...');

        // Fetch sales and products data
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

        // Get today's date explicitly, just in case it's null in the service
        $today = Carbon::today();

        // Aggregate sales data
        $salesAgg = $sales->groupBy('product_id')->map(function ($salesGroup) use ($today) {
            return $this->aggregateSalesData($salesGroup, $today);
        });

        $updates = [];

        // Loop through products and update their status
        foreach ($products as $product) {
            $stats = $salesAgg->get($product->id, [
                'total_quantity_sold' => 0,
                'days_since_last_sale' => 9999,
            ]);

            $status = $this->determineStatus($product, $stats);

            logger("🔍 Product {$product->id}: Status determined as {$status->value}");

            $updates[] = [
                'id' => $product->id,
                'inventory_status' => $status->value,
            ];
        }

        // Update product statuses in the database
        foreach ($updates as $update) {
            DB::table('products')
                ->where('id', $update['id'])
                ->update(['inventory_status' => $update['inventory_status']]);
        }

        logger('✅ Inventory Status Detection Completed. Updated ' . count($updates) . ' products.');
        Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock::status:');
    }
}
