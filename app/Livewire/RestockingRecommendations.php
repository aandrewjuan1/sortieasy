<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;

#[Title('Restocking Recommendations')]
class RestockingRecommendations extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $sortBy = 'name';

    #[Url(history: true)]
    public $sortDir = 'ASC';

    #[Computed]
    public function totalProducts(): int
    {
        return Product::count();
    }

    #[Computed]
    public function productsWithRecommendations(): int
    {
        return Product::has('restockingRecommendation')->count();
    }

    public function setSortBy($sortByField)
    {
        $isSameSortColumn = $this->sortBy === $sortByField;
        $this->sortBy = $sortByField;
        $this->sortDir = $isSameSortColumn ? ($this->sortDir == "ASC" ? 'DESC' : 'ASC') : 'ASC';
        $this->clearCurrentPageCache();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'perPage'])) {
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
        ]);
        $this->resetPage();
        $this->clearCurrentPageCache();
    }

    #[Computed]
    public function products()
    {
        $cacheKey = $this->getProductsCacheKey();

        return Cache::remember($cacheKey, now()->addMinutes(30), fn() => Product::query()
            ->with('restockingRecommendation')
            ->has('restockingRecommendation')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('sku', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage));
    }

    protected function getProductsCacheKey(): string
    {
        return sprintf(
            'restocking_recommendations:page:%d:per_page:%d:sort:%s:dir:%s:search:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search
        );
        // restocking_recommendations:page:1:per_page:10:sort:name:dir:ASC:search:
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getProductsCacheKey());
    }

    #[On('product-updated')]
    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }
}
