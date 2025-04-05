<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Logistic;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Title('Logistics')]
class Logistics extends Component
{
    use WithPagination;

    #[Url(history:true)]
    public $search = '';

    #[Url(history:true)]
    public $perPage = 10;

    #[Url(history:true)]
    public $statusFilter = '';

    #[Url(history:true)]
    public $sortBy = 'delivery_date';

    #[Url(history:true)]
    public $sortDir = 'DESC';

    public function setSortBy($sortByField)
    {
        if ($this->sortBy === $sortByField) {
            $this->sortDir = ($this->sortDir == "ASC") ? 'DESC' : "ASC";
            return;
        }

        $this->sortBy = $sortByField;
        $this->sortDir = 'DESC';  // Default to descending order
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    #[Computed]
    public function logistics()
    {
        return Logistic::with(['product'])
            ->search($this->search)
            ->ofStatus($this->statusFilter)
            ->orderBy($this->sortBy, $this->sortDir)  // Sorting by `quantity` or `delivery_date`
            ->paginate($this->perPage);
    }


    public function render()
    {
        return view('livewire.logistics');
    }
}
