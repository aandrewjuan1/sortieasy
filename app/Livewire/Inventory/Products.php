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
    public $sortBy = 'created_at'; // Changed default sort to created_at

    #[Url(history: true)]
    public $sortDir = 'DESC'; // Keep DESC for newest first

    #[Url(history: true)]
    public $categoryFilter = '';

    #[Url(history: true)]
    public $stockFilter = '';

    #[Computed(cache: true, key: 'product_categories')]
    public function categories()
    {
        return Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    public function setSortBy($sortByField)
    {
        // Check if the sort column is the same as the one clicked
        $isSameSortColumn = $this->sortBy === $sortByField;

        // If it's the same column, toggle the direction; otherwise, set the new column with default direction
        $this->sortBy = $sortByField;
        $this->sortDir = $isSameSortColumn ? ($this->sortDir == "ASC" ? 'DESC' : 'ASC') : 'DESC';

        // Clear the cache once, after sorting logic
        Cache::forget('products');
    }


    public function updated($property)
    {
        if (in_array($property, ['search', 'categoryFilter', 'stockFilter', 'perPage'])) {
            Cache::forget('products');
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

        Cache::forget('products');
    }

    #[Computed(cache: true, key: 'products')]
    public function products()
    {
        return Product::withSupplier()
            ->search($this->search)
            ->categoryFilter($this->categoryFilter)
            ->stockFilter($this->stockFilter)
            ->orderByField($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    #[On('product-deleted')]
    #[On('product-updated')]
    #[On('product-added')]
    public function reRender()
    {
        Cache::forget('product_categories');
        Cache::forget('products');
    }
}
