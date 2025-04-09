<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
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

        return Cache::remember($cacheKey, now()->addMinutes(30), function() {
            return Product::withSupplier()  // Eager load supplier and select name
                ->search($this->search)
                ->categoryFilter($this->categoryFilter)
                ->stockFilter($this->stockFilter)
                ->supplierFilter($this->supplierFilter)  // Apply supplier filter
                ->orderByField($this->sortBy, $this->sortDir)
                ->paginate($this->perPage);
        });
    }

    protected function getProductsCacheKey(): string
    {
        return sprintf(
            'products:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:category:%s:stock:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->categoryFilter,
            $this->supplierFilter,
            $this->stockFilter
        );
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
