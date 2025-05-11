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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

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

    public function downloadPdf()
    {
        try {
            // Count total records that would be included
            $totalRecords = Product::query()
                ->with('restockingRecommendation')
                ->has('restockingRecommendation')
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%')
                          ->orWhere('sku', 'like', '%'.$this->search.'%');
                    });
                })
                ->count();

            // If more than 1000 records, show warning
            if ($totalRecords > 1000) {
                $this->dispatch('notify',
                    type: 'warning',
                    message: 'The dataset is too large to download as PDF. Please apply more filters to reduce the number of records (currently ' . number_format($totalRecords) . ' records).'
                );
                return;
            }

            $allProducts = Product::query()
                ->with('restockingRecommendation')
                ->has('restockingRecommendation')
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%')
                          ->orWhere('sku', 'like', '%'.$this->search.'%');
                    });
                })
                ->orderBy($this->sortBy, $this->sortDir)
                ->get();

            $data = [
                'products' => $allProducts,
                'totalProducts' => $this->totalProducts,
                'productsWithRecommendations' => $this->productsWithRecommendations,
                'search' => $this->search,
                'generatedAt' => now()->format('Y-m-d H:i:s'),
            ];

            $pdf = PDF::loadView('pdf.restocking-recommendations', $data);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'restocking-recommendations.pdf');
        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Unable to generate PDF. The dataset might be too large. Please try applying more filters.'
            );
            Log::error('PDF Generation Error: ' . $e->getMessage());
        }
    }
}
