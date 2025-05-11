<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Jobs\RunForecasts;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\DemandForecast;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

#[Title('Demand Forecasts')]
class DemandForecasts extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $productFilter = null;

    #[Url(history: true)]
    public $dateRangeFilter = '';

    #[Url(history: true)]
    public $sortBy = 'forecast_date';

    #[Url(history: true)]
    public $sortDir = 'ASC';

    #[Computed]
    public function dateRangeOptions()
    {
        return [
            '' => 'All Dates',
            'today' => 'Today',
            'tomorrow' => 'Tomorrow',
            'week' => 'This Week',
            'month' => 'This Month',
            'quarter' => 'This Quarter',
            'year' => 'This Year',
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
        $isSameSortColumn = $this->sortBy === $sortByField;
        $this->sortBy = $sortByField;
        $this->sortDir = $isSameSortColumn ? ($this->sortDir == "ASC" ? 'DESC' : 'ASC') : 'DESC';
        $this->clearCurrentPageCache();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'productFilter', 'dateRangeFilter', 'perPage'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    #[Computed]
    public function totalForecasts(): int
    {
        return DemandForecast::count();
    }

    public function generateForecasts()
    {
        try {
            $this->authorize('runForecasts', DemandForecast::class);
            // Check if the forecasting has been run in the last 30 days
            $lastRunDate = Cache::get('forecasting_last_run');
            $today = now()->format('Y-m-d');

            // Calculate the date 30 days ago
            $thirtyDaysAgo = now()->subDays(30)->format('Y-m-d');

            // If the last run date is within the last 30 days, prevent running the forecast
            if ($lastRunDate && $lastRunDate >= $thirtyDaysAgo) {
                $this->dispatch('notify',
                    type: 'error',
                    message: 'Demand forecasting can only be run once every 30 days.'
                );
                return;
            }

            // Dispatch the forecast job
            RunForecasts::dispatch();

            // Store today's date in cache
            Cache::put('forecasting_last_run', $today, now()->addDays(30));

            // Dispatch a success notification
            $this->dispatch('notify',
                type: 'success',
                message: 'Demand forecasting is running in the background.'
            );

            Log::info('✅ Demand Forecasting Completed!');
        } catch (\Exception $e) {
            // If an error occurs, dispatch an error notification
            $this->dispatch('notify',
                type: 'error',
                message: 'Something went wrong.'
            );

            Log::error('❌ An error occurred: ' . $e->getMessage());
        }
    }

    public function canGenerateForecasts(): bool
    {
        // Get the date of the last run from the cache
        $lastRunDate = Cache::get('forecasting_last_run');
        $today = now()->format('Y-m-d');

        // Calculate the date 30 days ago
        $thirtyDaysAgo = now()->subDays(30)->format('Y-m-d');

        // If the last run date exists and is within the last 30 days, return false
        if ($lastRunDate && $lastRunDate >= $thirtyDaysAgo) {
            return false;  // Cannot generate forecasts if last run was within 30 days
        }

        return true;  // Can generate forecasts if it's been more than 30 days
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'productFilter',
            'dateRangeFilter',
            'perPage',
            'sortBy',
            'sortDir',
        ]);
        $this->resetPage();
        $this->clearCurrentPageCache();
    }

    #[Computed]
    public function forecasts()
    {
        $cacheKey = $this->getForecastCacheKey();

        return Cache::remember($cacheKey, now()->addMinutes(30), function() {
            return DemandForecast::with(['product'])
                ->search($this->search)
                ->forProduct($this->productFilter)
                ->forDateRange($this->dateRangeFilter)
                ->orderByColumn($this->sortBy, $this->sortDir)
                ->paginate($this->perPage);
        });
    }

    protected function getForecastCacheKey(): string
    {
        return sprintf(
            'demand_forecasts:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:product:%s:date_range:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->productFilter,
            $this->dateRangeFilter
        );
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getForecastCacheKey());
    }

    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }

    public function downloadPdf()
    {
        try {
            // Count total records that would be included
            $totalRecords = DemandForecast::with(['product'])
                ->search($this->search)
                ->forProduct($this->productFilter)
                ->forDateRange($this->dateRangeFilter)
                ->count();

            // If more than 1000 records, show warning
            if ($totalRecords > 1000) {
                $this->dispatch('notify',
                    type: 'warning',
                    message: 'The dataset is too large to download as PDF. Please apply more filters to reduce the number of records (currently ' . number_format($totalRecords) . ' records).'
                );
                return;
            }

            $forecasts = DemandForecast::with(['product'])
                ->search($this->search)
                ->forProduct($this->productFilter)
                ->forDateRange($this->dateRangeFilter)
                ->orderByColumn($this->sortBy, $this->sortDir)
                ->get();

            $pdf = Pdf::loadView('pdf.demand-forecasts', [
                'forecasts' => $forecasts,
                'generatedAt' => now()->format('Y-m-d H:i:s'),
                'filters' => [
                    'search' => $this->search,
                    'product' => $this->productFilter ? $this->products[$this->productFilter] : null,
                    'dateRange' => $this->dateRangeFilter ? $this->dateRangeOptions[$this->dateRangeFilter] : null,
                ]
            ]);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'demand-forecasts.pdf');
        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Unable to generate PDF. The dataset might be too large. Please try applying more filters.'
            );
            Log::error('PDF Generation Error: ' . $e->getMessage());
        }
    }
}
