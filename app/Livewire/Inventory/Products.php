<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Renderless;
use Illuminate\Support\Facades\Cache;
use App\Jobs\RunInventoryStatusDetection;
use App\Events\InventoryStatusDetectionCompleted;

#[Title('Products')]
class Products extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $sortBy = 'created_at';

    #[Url(history: true)]
    public $sortDir = 'DESC';

    #[Url(history: true)]
    public $categoryFilter = '';
    #[Url(history: true)]
    public $supplierFilter = '';

    #[Url(history: true)]
    public $stockFilter = '';

    #[Url(history: true)]
    public $statusFilter = '';

    #[Computed]
    public function totalInventoryValue(): float
    {
        return Product::query()
            ->search($this->search)
            ->categoryFilter($this->categoryFilter)
            ->supplierFilter($this->supplierFilter)
            ->stockFilter($this->stockFilter)
            ->sum(DB::raw('quantity_in_stock * price'));
    }

    public function runDetection()
    {
        try {
            // Check if the button was clicked today
            $lastRunDate = Cache::get('inventory_detection_last_run');
            $today = now()->format('Y-m-d');

            if ($lastRunDate === $today) {
                $this->dispatch('notify',
                    type: 'error',
                    message: 'Inventory detection can only be run once per day'
                );
                return;
            }

            RunInventoryStatusDetection::dispatch();

            // Store today's date in cache
            Cache::put('inventory_detection_last_run', $today, now()->addDay());

            $this->dispatch('notify',
                type: 'success',
                message: 'Inventory status detection is running in the background.'
            );
        } catch (\Exception $e) {
            // Handle the exception
            $this->dispatch('notify',
                type: 'error',
                message: 'An error occurred: ' . $e->getMessage()
            );
        }
    }

    public function canRunDetection()
    {
        $lastRunDate = Cache::get('inventory_detection_last_run');
        $today = now()->format('Y-m-d');

        return $lastRunDate !== $today;
    }


    #[Computed]
    public function lowStockCount(): int
    {
        return Product::query()
            ->search($this->search)
            ->categoryFilter($this->categoryFilter)
            ->supplierFilter($this->supplierFilter)
            // Low stock: safety_stock < quantity_in_stock <= reorder_threshold
            ->whereColumn('quantity_in_stock', '>', 'safety_stock')
            ->whereColumn('quantity_in_stock', '<=', 'reorder_threshold')
            ->count();
    }

    #[Computed]
    public function criticalStockCount(): int
    {
        return Product::query()
            ->search($this->search)
            ->categoryFilter($this->categoryFilter)
            ->supplierFilter($this->supplierFilter)
            // Critical stock: quantity_in_stock <= safety_stock
            ->whereColumn('quantity_in_stock', '<=', 'safety_stock')
            ->count();
    }

    #[Computed]
    public function outOfStockCount(): int
    {
        return Product::query()
            ->search($this->search)
            ->categoryFilter($this->categoryFilter)
            ->supplierFilter($this->supplierFilter)
            ->where('quantity_in_stock', 0)
            ->count();
    }

    #[Computed]
    public function averageProfitMargin(): float
    {
        $result = Product::query()
            ->search($this->search)
            ->categoryFilter($this->categoryFilter)
            ->supplierFilter($this->supplierFilter)
            ->where('cost', '>', 0)
            ->selectRaw('AVG(((price - cost) / price) * 100) as avg_margin')
            ->first();

        return (float) ($result->avg_margin ?? 0);
    }

    public function setSortBy($sortByField)
    {
        $isSameSortColumn = $this->sortBy === $sortByField;
        $this->sortBy = $sortByField;
        $this->sortDir = $isSameSortColumn ? ($this->sortDir == "ASC" ? 'DESC' : 'ASC') : 'DESC';
        $this->clearCurrentPageCache();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'categoryFilter', 'supplierFilter', 'stockFilter', 'statusFilter', 'perPage'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'categoryFilter',
            'supplierFilter',
            'stockFilter',
            'statusFilter',
            'perPage',
            'sortBy',
            'sortDir',
        ]);
        $this->resetPage();
        $this->clearCurrentPageCache();
    }

    #[Computed()]
    public function products()
    {
        $cacheKey = $this->getProductsCacheKey();

        return Cache::remember($cacheKey, now()->addMinutes(30), fn() => Product::withSupplier()
            ->search($this->search)
            ->categoryFilter($this->categoryFilter)
            ->stockFilter($this->stockFilter)
            ->supplierFilter($this->supplierFilter)
            ->statusFilter($this->statusFilter) // Add this line
            ->orderByField($this->sortBy, $this->sortDir)
            ->paginate($this->perPage));
    }

    #[Computed]
    public function totalProducts(): int
    {
        return Product::count();
    }

    #[Computed]
    public function totalStocks(): int
    {
        return Product::sum('quantity_in_stock');
    }


    protected function getProductsCacheKey(): string
    {
        return sprintf(
            'products:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:category:%s:supplier:%s:stock:%s:status:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->categoryFilter,
            $this->supplierFilter,
            $this->stockFilter,
            $this->statusFilter
        );
        // products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock::status:
    }

    #[On('inventoryStatusDetectionCompleted')]
    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getProductsCacheKey());
    }

    #[On('product-deleted')]
    #[On('product-updated')]
    #[On('product-added')]
    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }
}
