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
use App\Services\ForecastService;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

#[Title('Demand Forecasts')]
class DemandForecasts extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $productFilter = '';

    #[Url(history: true)]
    public $dateRangeFilter = '';

    #[Url(history: true)]
    public $sortBy = 'forecast_date';

    #[Url(history: true)]
    public $sortDir = 'DESC';

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
            'future' => 'Future Dates',
            'past' => 'Past Dates',
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

    #[Computed]
    public function futureForecastsCount(): int
    {
        return DemandForecast::where('forecast_date', '>', now())->count();
    }

    #[Computed]
    public function pastForecastsCount(): int
    {
        return DemandForecast::where('forecast_date', '<=', now())->count();
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

        return Cache::remember($cacheKey, now()->addMinutes(30), fn() => DemandForecast::with(['product'])
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->productFilter, fn ($query) => $query->where('product_id', $this->productFilter))
            ->when($this->dateRangeFilter, function ($query) {
                match ($this->dateRangeFilter) {
                    'today' => $query->whereDate('forecast_date', today()),
                    'tomorrow' => $query->whereDate('forecast_date', today()->addDay()),
                    'week' => $query->whereBetween('forecast_date', [now()->startOfWeek(), now()->endOfWeek()]),
                    'month' => $query->whereBetween('forecast_date', [now()->startOfMonth(), now()->endOfMonth()]),
                    'quarter' => $query->whereBetween('forecast_date', [now()->startOfQuarter(), now()->endOfQuarter()]),
                    'year' => $query->whereBetween('forecast_date', [now()->startOfYear(), now()->endOfYear()]),
                    'future' => $query->where('forecast_date', '>', now()),
                    'past' => $query->where('forecast_date', '<=', now()),
                };
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage));
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

    #[On('forecast-created')]
    #[On('forecast-updated')]
    #[On('forecast-deleted')]
    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }
}
