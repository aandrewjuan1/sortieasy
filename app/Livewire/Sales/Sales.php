<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Sale;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use App\Enums\SaleChannel;

#[Title('Sales')]
class Sales extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $channelFilter = '';

    #[Url(history: true)]
    public $dateFilter = '';

    #[Url(history: true)]
    public $sortBy = 'sale_date';

    #[Url(history: true)]
    public $sortDir = 'DESC';

    #[Computed]
    public function dateFilterOptions()
    {
        return [
            '' => 'All Time',
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    #[Computed]
    public function channelOptions()
    {
        return [
            '' => 'All Channels',
            SaleChannel::Online->value => SaleChannel::getLabel(SaleChannel::Online->value),
            SaleChannel::InStore->value => SaleChannel::getLabel(SaleChannel::InStore->value),
            SaleChannel::Phone->value => SaleChannel::getLabel(SaleChannel::Phone->value),
        ];
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
        if (in_array($property, ['search', 'channelFilter', 'dateFilter', 'perPage'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'channelFilter',
            'dateFilter',
            'perPage',
            'sortBy',
            'sortDir',
        ]);
        $this->resetPage();
        $this->clearCurrentPageCache();
    }

    #[Computed]
    public function todaySalesCount(): int
    {
        return Sale::whereDate('sale_date', today())->count();
    }

    #[Computed]
    public function thisWeekSalesCount(): int
    {
        return Sale::whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
    }

    #[Computed]
    public function thisMonthSalesCount(): int
    {
        return Sale::whereBetween('sale_date', [now()->startOfMonth(), now()->endOfMonth()])->count();
    }

    #[Computed]
    public function onlineSalesCount(): int
    {
        return Sale::where('channel', SaleChannel::Online->value)->count();
    }

    #[Computed]
    public function inStoreSalesCount(): int
    {
        return Sale::where('channel', SaleChannel::InStore->value)->count();
    }

    #[Computed]
    public function phoneSalesCount(): int
    {
        return Sale::where('channel', SaleChannel::Phone->value)->count();
    }

    #[Computed]
    public function totalRevenue(): float
    {
        return Sale::sum('total_price');
    }

    #[Computed]
    public function todayRevenue(): float
    {
        return Sale::whereDate('sale_date', today())->sum('total_price');
    }

    #[Computed]
    public function totalSalesCount(): int
    {
        return Sale::count();
    }

    #[Computed]
    public function thisWeekRevenue(): float
    {
        return Sale::whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_price');
    }

    #[Computed]
    public function thisMonthRevenue(): float
    {
        return Sale::whereBetween('sale_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_price');
    }

    #[Computed]
    public function sales()
    {
        $cacheKey = $this->getSalesCacheKey();

        return Cache::remember($cacheKey, now()->addMinutes(30), fn () =>
            Sale::with(['product', 'user'])
                ->search($this->search)
                ->filterByChannel($this->channelFilter)
                ->filterByDate($this->dateFilter)
                ->orderBy($this->sortBy, $this->sortDir)
                ->paginate($this->perPage)
        );
    }


    protected function getSalesCacheKey(): string
    {
        return sprintf(
            'sales:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:channel:%s:date:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->channelFilter,
            $this->dateFilter
        );

        // sales:page:1:per_page:10:sort:created_at:dir:DESC:search::channel::date:
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getSalesCacheKey());
    }

    #[On('sale-deleted')]
    #[On('sale-updated')]
    #[On('sale-added')]
    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }
}
