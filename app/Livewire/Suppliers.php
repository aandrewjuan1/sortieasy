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
    public $sortBy = 'name';

    #[Url(history: true)]
    public $sortDir = 'ASC';

    #[Url(history: true)]
    public $productFilter = '';

    #[Computed]
    public function productOptions()
    {
        return \App\Models\Product::select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');
    }

    public function setSortBy($sortByField)
    {
        if ($this->sortBy === $sortByField) {
            $this->sortDir = ($this->sortDir == "ASC") ? 'DESC' : "ASC";
            return;
        }

        $this->sortBy = $sortByField;
        $this->sortDir = 'ASC';
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'productFilter', 'perPage'])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::with(['products', 'latestDelivery'])
            ->search($this->search)
            ->when($this->productFilter, function($query) {
                $query->whereHas('products', function($q) {
                    $q->where('name', $this->productFilter);
                });
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.suppliers');
    }
}
