<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use App\Models\RestockingRecommendation;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
class ProductSummary extends Component
{
    #[Computed]
    public function products(): Collection
    {
        return Product::select([
            'id',
            'name',
            'quantity_in_stock',
            'reorder_threshold',
            'suggested_reorder_threshold',
            'suggested_safety_stock',
            'safety_stock'
        ])
        ->with('restockingRecommendations') // eager load if you add a relationship
        ->orderBy('name')
        ->get();
    }

    #[Computed]
    public function totalProducts(): int
    {
        return $this->products->count();
    }

    #[Computed]
    public function totalStocks(): int
    {
        return $this->products->sum('quantity_in_stock');
    }

    #[Computed]
    public function lowStockProducts(): Collection
    {
        return $this->products->filter(fn ($p) => $this->isLowStock($p));
    }

    #[Computed]
    public function outOfStockProducts(): Collection
    {
        return $this->products->filter(fn ($p) => $this->isOutOfStock($p));
    }

    #[Computed]
    public function criticalStockProducts(): Collection
    {
        return $this->products->filter(fn ($p) => $this->isCriticalStock($p))
                              ->sortBy('quantity_in_stock');
    }

    #[Computed]
    public function overstockedProducts(): Collection
    {
        return $this->products->filter(fn ($p) => $this->isOverstocked($p))
                              ->sortByDesc('quantity_in_stock');
    }

    // Reusable conditions
    private function isLowStock($p): bool
    {
        return $p->quantity_in_stock <= $p->reorder_threshold &&
               $p->quantity_in_stock > $p->safety_stock;
    }

    private function isOutOfStock($p): bool
    {
        return $p->quantity_in_stock === 0;
    }

    private function isCriticalStock($p): bool
    {
        return $p->quantity_in_stock <= $p->safety_stock;
    }

    private function isOverstocked($p): bool
    {
        // Option 3: Combination approach
        $overstockMultiplier = 3;
        $baseThreshold = $p->reorder_threshold * $overstockMultiplier;

        if ($p->restockingRecommendations->isNotEmpty()) {
            $forecastedDemand = $p->restockingRecommendations->first()->total_forecasted_demand ?? 0;
            return $p->quantity_in_stock > max($baseThreshold, $forecastedDemand * 1.5);
        }

        return $p->quantity_in_stock > $baseThreshold;
    }
}
