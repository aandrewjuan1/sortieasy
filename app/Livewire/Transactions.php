<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Carbon;

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

    public function setSortBy($sortByField)
    {
        if ($this->sortBy === $sortByField) {
            $this->sortDir = ($this->sortDir == "ASC") ? 'DESC' : "ASC";
            return;
        }

        $this->sortBy = $sortByField;
        $this->sortDir = 'DESC';
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'typeFilter', 'dateFilter', 'perPage'])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function transactions()
    {
        return Transaction::with(['product', 'user'])
            ->search($this->search)
            ->ofType($this->typeFilter)
            ->when($this->dateFilter, function($query) {
                $now = Carbon::now();
                switch($this->dateFilter) {
                    case 'today':
                        return $query->whereDate('created_at', $now->toDateString());
                    case 'yesterday':
                        return $query->whereDate('created_at', $now->subDay()->toDateString());
                    case 'week':
                        return $query->whereBetween('created_at', [
                            $now->startOfWeek()->toDateTimeString(),
                            $now->endOfWeek()->toDateTimeString()
                        ]);
                    case 'month':
                        return $query->whereBetween('created_at', [
                            $now->startOfMonth()->toDateTimeString(),
                            $now->endOfMonth()->toDateTimeString()
                        ]);
                    case 'year':
                        return $query->whereBetween('created_at', [
                            $now->startOfYear()->toDateTimeString(),
                            $now->endOfYear()->toDateTimeString()
                        ]);
                }
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.transactions');
    }
}
