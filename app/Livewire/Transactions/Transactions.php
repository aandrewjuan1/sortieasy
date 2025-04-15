<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;

#[Title('Transactions')]
class Transactions extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $typeFilter = '';

    #[Url(history: true)]
    public $dateFilter = '';

    #[Url(history: true)]
    public $sortBy = 'created_at';

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
    public function types()
    {
        return [
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'return' => 'Return',
            'adjustment' => 'Adjustment',
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
        if (in_array($property, ['search', 'typeFilter', 'dateFilter', 'perPage'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    #[Computed]
    public function purchaseCount(): int
    {
        return Transaction::where('type', 'purchase')->count();
    }

    #[Computed]
    public function saleCount(): int
    {
        return Transaction::where('type', 'sale')->count();
    }

    #[Computed]
    public function returnCount(): int
    {
        return Transaction::where('type', 'return')->count();
    }

    #[Computed]
    public function adjustmentCount(): int
    {
        return Transaction::where('type', 'adjustment')->count();
    }

    #[Computed]
    public function positiveQuantityCount(): int
    {
        return Transaction::where('quantity', '>', 0)->count();
    }

    #[Computed]
    public function negativeQuantityCount(): int
    {
        return Transaction::where('quantity', '<', 0)->count();
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'typeFilter',
            'dateFilter',
            'perPage',
            'sortBy',
            'sortDir',
        ]);
        $this->resetPage();
        $this->clearCurrentPageCache();
    }


    #[Computed]
    public function transactions()
    {
        $cacheKey = $this->getTransactionCacheKey();

        return Cache::remember($cacheKey, now()->addMinutes(30), fn() => Transaction::withProductAndUser()
            ->search($this->search)
            ->ofType($this->typeFilter)
            ->dateFilter($this->dateFilter)
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage));
    }

    #[Computed]
    public function totalTransactions()
    {
        return Transaction::count();
    }

    protected function getTransactionCacheKey(): string
    {
        return sprintf(
            'transactions:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:type:%s:date:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->typeFilter,
            $this->dateFilter
        );

        // transactions:page:1:per_page:10:sort:created_at:dir:DESC:search::type::date:
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getTransactionCacheKey());
    }

    #[On('transaction-deleted')]
    #[On('transaction-updated')]
    #[On('transaction-added')]
    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }
}
