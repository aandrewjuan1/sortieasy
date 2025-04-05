<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
class ProductSummary extends Component
{
    #[Computed(persist: true, cache: true)]
    public function products(): Collection
    {
        return Product::select([
            'id',
            'name',
            'quantity_in_stock',
            'reorder_threshold',
            'safety_stock'
        ])
        ->orderBy('name')
        ->get();
    }

    #[Computed]
    public function totalProducts(): int
    {
        return $this->products->count();
    }

    #[Computed]
    public function totalStock(): int
    {
        return $this->products->sum('quantity_in_stock');
    }

    #[Computed]
    public function lowStockProducts(): Collection
    {
        return $this->products->filter(fn ($p) =>
            $p->quantity_in_stock < $p->reorder_threshold
        )->sortBy('quantity_in_stock');
    }

    #[Computed]
    public function safetyStockProducts(): Collection
    {
        return $this->products->filter(fn ($p) =>
            $p->quantity_in_stock < $p->safety_stock
        )->sortBy('quantity_in_stock');
    }

    #[Computed]
    public function overstockedProducts(): Collection
    {
        return $this->products->filter(fn ($p) =>
            $p->quantity_in_stock > ($p->reorder_threshold * 2)
        )->sortByDesc('quantity_in_stock');
    }

    #[Computed]
    public function restockRecommendations(): Collection
    {
        return $this->products
            ->filter(fn ($p) =>
                $p->quantity_in_stock < $p->safety_stock ||
                $p->quantity_in_stock < $p->reorder_threshold
            )
            ->sortBy('quantity_in_stock')
            ->unique('id');
    }
}
