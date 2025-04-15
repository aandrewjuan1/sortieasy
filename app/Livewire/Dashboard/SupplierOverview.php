<?php

namespace App\Livewire\Dashboard;

use App\Models\Supplier;
use App\Models\Logistic;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
class SupplierOverview extends Component
{
    #[Computed]
    public function totalSuppliers(): int
    {
        return Supplier::count();
    }

    #[Computed]
    public function recentDeliveries()
    {
        return Logistic::with(['product.supplier'])
            ->where('status', 'delivered')
            ->where('delivery_date', '>=', now()->subMonth())
            ->latest('delivery_date')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function topSuppliers()
    {
        return Supplier::withCount('products')
            ->orderByDesc('products_count')
            ->limit(5)
            ->get();
    }
}
