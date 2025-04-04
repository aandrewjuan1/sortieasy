<?php

namespace App\Livewire\Dashboard;

use App\Models\Supplier;
use App\Models\Logistic;
use App\Models\Product; // Add this if using the alternative approach
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon; // Add this import at the top

class SupplierOverview extends Component
{
    // Cache time in seconds (5 minutes)
    protected const CACHE_TIME = 300;

    #[Computed(persist: true, cache: true)]
    public function totalSuppliers(): int
    {
        return Cache::remember('total_suppliers', self::CACHE_TIME, fn() => Supplier::count());
    }

    // In your SupplierOverview component
    #[Computed(persist: true, cache: true)]
    public function suppliersWithProductCount()
    {
        return Cache::remember('suppliers_with_products', self::CACHE_TIME,
            fn() => Supplier::withCount('products')
                ->with(['latestDelivery']) // If you added the relationship to Supplier model
                ->orderBy('name')
                ->get()
        );
    }

    #[Computed(persist: true, cache: true)]
    public function recentDeliveries()
    {
        return Cache::remember('recent_deliveries', self::CACHE_TIME,
            fn() => Logistic::with(['product.supplier'])
                ->where('status', 'delivered')
                ->where('delivery_date', '>=', now()->subMonth())
                ->latest('delivery_date')
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
    public function render()
    {
        return view('livewire.dashboard.supplier-overview');
    }
}
