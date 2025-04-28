<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use App\Models\Transaction;
use App\Models\AnomalyDetectionResult;

#[Title('Anomalous Transactions')]
class AnomalousTransactions extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $sortBy = 'created_at';

    #[Url(history: true)]
    public $sortDir = 'ASC';

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
    public function totalAnomalousTransactions(): int
    {
        return AnomalyDetectionResult::where('status', 'anomalous')->count();
    }

    #[Computed]
    public function anomalousTransactions()
    {
        $cacheKey = $this->getAnomalousTransactionsCacheKey();

        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            return Transaction::with('anomalyDetectionResult')
                ->whereHas('anomalyDetectionResult', function ($query) {
                    $query->where('status', 'anomalous');
                })
                ->when($this->search, function ($query) {
                    $query->where('description', 'like', '%' . $this->search . '%')
                          ->orWhere('id', 'like', '%' . $this->search . '%');
                })
                ->orderBy($this->sortBy, $this->sortDir)
                ->paginate($this->perPage);
        });
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'perPage', 'sortBy', 'sortDir'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    public function setSortBy($sortByField)
    {
        $isSameSortColumn = $this->sortBy === $sortByField;
        $this->sortBy = $sortByField;
        $this->sortDir = $isSameSortColumn ? ($this->sortDir == "ASC" ? 'DESC' : 'ASC') : 'DESC';
        $this->clearCurrentPageCache();
    }

    protected function getAnomalousTransactionsCacheKey(): string
    {
        return sprintf(
            'anomalous_transactions:page:%d:per_page:%d:sort:%s:dir:%s:search:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search
        );
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getAnomalousTransactionsCacheKey());
    }

    #[On('anomaly-detected')]
    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }
}
