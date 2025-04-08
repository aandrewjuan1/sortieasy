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
        if (in_array($property, ['search', 'categoryFilter', 'stockFilter', 'perPage'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'categoryFilter',
            'stockFilter',
            'perPage',
            'sortBy',
            'sortDir',
        ]);

        $this->clearCurrentPageCache();
    }

    #[Computed()]
    public function products()
    {
        $cacheKey = $this->generateProductsCacheKey();

        return Cache::remember($cacheKey, 300, function() {
            return Product::withSupplier()
                    ->search($this->search)
                    ->categoryFilter($this->categoryFilter)
                    ->stockFilter($this->stockFilter)
                    ->orderByField($this->sortBy, $this->sortDir)
                    ->paginate($this->perPage);
        });
    }

    #[On('product-deleted')]
    #[On('product-updated')]
    #[On('product-added')]
    public function reRender()
    {
        $this->clearCurrentPageCache();
    }

    // Helper methods for cache key generation
    protected function generateProductsCacheKey(): string
    {
        return sprintf(
            'products_page_%s_%s_%s_%s_%s',
            $this->getPage(),
            $this->perPage,
            md5($this->search),
            $this->categoryFilter,
            $this->stockFilter
        );
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->generateProductsCacheKey());
    }
}
