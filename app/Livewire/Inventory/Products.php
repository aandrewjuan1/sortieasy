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
use Illuminate\Support\Facades\Cache;

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

    #[Computed]
    public function lowStockCount(): int
    {
        return Product::query()
            ->search($this->search)
            ->categoryFilter($this->categoryFilter)
            ->supplierFilter($this->supplierFilter)
            ->whereColumn('quantity_in_stock', '<=', 'reorder_threshold')
            ->whereColumn('quantity_in_stock', '>', 'safety_stock')
            ->count();
    }

    #[Computed]
    public function criticalStockCount(): int
    {
        return Product::query()
            ->search($this->search)
            ->categoryFilter($this->categoryFilter)
            ->supplierFilter($this->supplierFilter)
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
        if (in_array($property, ['search', 'categoryFilter', 'supplierFilter', 'stockFilter', 'perPage'])) {
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
            'products:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:category:%s:supplier:%s:stock:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->categoryFilter,
            $this->supplierFilter,
            $this->stockFilter
        );

        // products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock:
    }

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
