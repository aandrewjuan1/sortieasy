<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use App\Models\RestockingRecommendation;

#[Title('Dashboard')]
class ProductSummary extends Component
{
    #[Computed]
    #[On('product-updated')]
    public function products(): Collection
    {
        return Product
        ::with('restockingRecommendation') // eager load if you add a relationship
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
        // Assuming there's a relationship to restocking recommendations
        $recommendation = $p->restockingRecommendation;

        // If no restocking recommendation exists, we might consider it as not overstocked
        if (!$recommendation) {
            return false;
        }

        // Calculate the reorder threshold and forecasted demand
        $forecastedDemand = $recommendation->total_forecasted_demand;
        $reorderThreshold = $p->suggested_reorder_threshold;

        // Consider overstocked if the quantity is greater than the reorder threshold
        // and the stock exceeds the demand forecast by a significant amount
        return $p->quantity_in_stock > ($reorderThreshold + $forecastedDemand);
    }
}
