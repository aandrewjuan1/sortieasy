<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Enums\AnomalyStatus;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Jobs\RunAnomalyDetection;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AnomalyDetectionResult;
use Barryvdh\DomPDF\Facade\Pdf;

#[Title('Anomalous Transactions')]
class AnomalousTransactions extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $productFilter = null;

    #[Url(history: true)]
    public $showOnlyAnomalies = true;

    #[Url(history: true)]
    public $sortBy = 'transaction_id';

    #[Url(history: true)]
    public $sortDir = 'DESC';

    #[Computed]
    public function filterOptions()
    {
        return [
            true => 'Show Only Anomalies',
            false => 'Show All Results',
        ];
    }

    #[Computed]
    public function products()
    {
        return Product::orderBy('name')
            ->get(['id', 'name', 'sku'])
            ->mapWithKeys(fn ($product) => [
                $product->id => "{$product->name} ({$product->sku})"
            ]);
    }

    public function setSortBy($sortByField)
    {
        if ($this->sortBy === $sortByField) {
            $this->sortDir = $this->sortDir === 'ASC' ? 'DESC' : 'ASC';
        } else {
            $this->sortDir = 'DESC';
        }
        $this->sortBy = $sortByField;
        $this->clearCurrentPageCache();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'productFilter', 'showOnlyAnomalies', 'perPage'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    #[Computed]
    public function totalAnomalies(): int
    {
        return AnomalyDetectionResult::where('status', AnomalyStatus::Anomalous->value)->count();
    }

    public function detectAnomaly()
    {
        try {
            // Dispatch the forecast job
            RunAnomalyDetection::dispatch();

            // Dispatch a success notification
            $this->dispatch('notify',
                type: 'success',
                message: 'Anomaly detection is running in the background.'
            );

            Log::info('✅ Anomaly detection Completed!');
        } catch (\Exception $e) {
            // If an error occurs, dispatch an error notification
            $this->dispatch('notify',
                type: 'error',
                message: 'Something went wrong.'
            );

            Log::error('❌ An error occurred: ' . $e->getMessage());
        }
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'productFilter',
            'showOnlyAnomalies',
            'perPage',
            'sortBy',
            'sortDir',
        ]);
        $this->resetPage();
        $this->clearCurrentPageCache();
    }

    #[Computed]
    public function results()
    {
        $cacheKey = $this->getResultsCacheKey();

        return Cache::remember($cacheKey, now()->addMinutes(15), function() {
            return AnomalyDetectionResult::with(['product', 'transaction'])
                ->when($this->showOnlyAnomalies, function ($query) {
                    $query->where('status', AnomalyStatus::Anomalous->value);
                })
                ->when($this->search, function ($query) {
                    $query->whereHas('product', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('sku', 'like', '%' . $this->search . '%')
                          ->orWhere('id', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->productFilter, function ($query) {
                    $query->where('product_id', $this->productFilter);
                })
                ->orderBy($this->sortBy, $this->sortDir)
                ->paginate($this->perPage);
        });
    }

    protected function getResultsCacheKey(): string
    {
        return sprintf(
            'anomaly_results:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:product:%s:anomalies_only:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->productFilter,
            $this->showOnlyAnomalies
        );

        // anomaly_results:page:1:per_page:10:sort:transaction_id:dir:DESC:search::product::anomalies_only:true:
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getResultsCacheKey());
    }

    public function downloadPdf()
    {
        try {
            // Count total records that would be included
            $totalRecords = AnomalyDetectionResult::with(['product', 'transaction'])
                ->when($this->showOnlyAnomalies, function ($query) {
                    $query->where('status', AnomalyStatus::Anomalous->value);
                })
                ->when($this->search, function ($query) {
                    $query->whereHas('product', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('sku', 'like', '%' . $this->search . '%')
                          ->orWhere('id', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->productFilter, function ($query) {
                    $query->where('product_id', $this->productFilter);
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

            $allResults = AnomalyDetectionResult::with(['product', 'transaction'])
                ->when($this->showOnlyAnomalies, function ($query) {
                    $query->where('status', AnomalyStatus::Anomalous->value);
                })
                ->when($this->search, function ($query) {
                    $query->whereHas('product', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('sku', 'like', '%' . $this->search . '%')
                          ->orWhere('id', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->productFilter, function ($query) {
                    $query->where('product_id', $this->productFilter);
                })
                ->orderBy($this->sortBy, $this->sortDir)
                ->get();

            $data = [
                'results' => $allResults,
                'totalAnomalies' => $this->totalAnomalies,
                'search' => $this->search,
                'productFilter' => $this->productFilter ? $this->products[$this->productFilter] : null,
                'showOnlyAnomalies' => $this->showOnlyAnomalies,
                'generatedAt' => now()->format('Y-m-d H:i:s'),
            ];

            $pdf = PDF::loadView('pdf.anomalous-transactions', $data);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'anomalous-transactions.pdf');
        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Unable to generate PDF. The dataset might be too large. Please try applying more filters.'
            );
            Log::error('PDF Generation Error: ' . $e->getMessage());
        }
    }
}
