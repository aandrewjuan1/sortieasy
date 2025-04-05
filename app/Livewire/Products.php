<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

#[Title('Products')]
class Products extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $sortBy = 'name';

    #[Url(history: true)]
    public $sortDir = 'DESC';

    #[Url(history: true)]
    public $categoryFilter = '';

    #[Url(history: true)]
    public $stockFilter = '';

    #[Computed]
    public function categories()
    {
        return Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
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
        if (in_array($property, ['search', 'categoryFilter', 'stockFilter', 'perPage'])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function products()
    {
        return Product::withSupplier()
            ->search($this->search)
            ->categoryFilter($this->categoryFilter)
            ->stockFilter($this->stockFilter)
            ->orderByField($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }



    public function render()
    {
        return view('livewire.products');
    }
}
