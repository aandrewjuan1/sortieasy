<?php

namespace App\Services;

use App\Enums\InventoryStatus;
use Carbon\Carbon;

class InventoryStatusService
{
    const SLOW_MOVING_DAYS_THRESHOLD = 30;  // New: Days since last sale to consider "Slow Moving"
    const OBSOLETE_DAYS_THRESHOLD = 90;     // Days since last sale to consider "Obsolete"

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
}
