<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;

#[Title('Suppliers')]
class Suppliers extends Component
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
    public $productFilter = '';

    public function setSortBy($sortByField)
    {
        $isSameSortColumn = $this->sortBy === $sortByField;
        $this->sortBy = $sortByField;
        $this->sortDir = $isSameSortColumn ? ($this->sortDir == "ASC" ? 'DESC' : 'ASC') : 'DESC';
        $this->clearCurrentPageCache();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'perPage', 'productFilter'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'perPage',
            'sortBy',
            'sortDir',
            'productFilter',
        ]);
        $this->resetPage();
        $this->clearCurrentPageCache();
    }

    #[Computed()]
    public function suppliers()
    {
        $cacheKey = $this->getSuppliersCacheKey();

        return Cache::remember($cacheKey, now()->addMinutes(30), function() {
            return Supplier::with(['products' => function($query) {
                        $query->select('id', 'name', 'supplier_id');
                    }])
                    ->search($this->search)
                    ->when($this->productFilter, function($query) {
                        $query->whereHas('products', function($q) {
                            $q->where('name', 'like', "%{$this->productFilter}%");
                        });
                    })
                    ->orderBy($this->sortBy, $this->sortDir)
                    ->paginate($this->perPage);
        });
    }

    protected function getSuppliersCacheKey(): string
    {
        return sprintf(
            'suppliers:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:product:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->productFilter
        );
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getSuppliersCacheKey());
    }

    #[On('supplier-deleted')]
    #[On('supplier-updated')]
    #[On('supplier-added')]
    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }
}
