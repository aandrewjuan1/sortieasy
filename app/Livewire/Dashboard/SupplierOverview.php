<?php

namespace App\Livewire\Dashboard;

use App\Models\Supplier;
use App\Models\Logistic;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
class SupplierOverview extends Component
{
    // Cache time in seconds (5 minutes)
    protected const CACHE_TIME = 300;

    #[Computed]
    public function totalSuppliers(): int
    {
        return Cache::remember('total_suppliers', self::CACHE_TIME, fn() => Supplier::count());
    }

    #[Computed]
    public function recentDeliveries()
    {
        return Cache::remember('recent_deliveries', self::CACHE_TIME,
            fn() => Logistic::with(['product.supplier'])
                ->where('status', 'delivered')
                ->where('delivery_date', '>=', now()->subMonth())
                ->latest('delivery_date')  // This sorts by delivery_date in descending order
                ->limit(10)
                ->get()
        );
    }


    #[Computed]
    public function topSuppliers()
    {
        return Cache::remember('top_suppliers', self::CACHE_TIME,
            fn() => Supplier::withCount('products')
                ->orderByDesc('products_count')
                ->limit(5)
                ->get()
        );
    }
}
