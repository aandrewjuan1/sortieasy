<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Supplier;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Title('Suppliers')]
class Suppliers extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $sortBy = 'created_at';

    #[Url(history: true)]
    public $sortDir = 'DESC';

    #[Url(history: true)]
    public $productFilter = '';

    public function setSortBy($sortByField)
    {
        // Check if the sort column is the same as the one clicked
        $isSameSortColumn = $this->sortBy === $sortByField;

        // If it's the same column, toggle the direction; otherwise, set the new column with default direction
        $this->sortBy = $sortByField;
        $this->sortDir = $isSameSortColumn ? ($this->sortDir == "ASC" ? 'DESC' : 'ASC') : 'DESC';
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'productFilter', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'productFilter',
            'perPage',
            'sortBy',
            'sortDir',
        ]);
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::with(['products:id,name', 'latestDelivery' => function($query) {
            $query->orderBy('delivery_date', 'desc')->limit(1);
        }])
        ->search($this->search)
        ->orderByField($this->sortBy, $this->sortDir)
        ->paginate($this->perPage);
    }
}
