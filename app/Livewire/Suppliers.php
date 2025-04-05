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

    #[Url(history:true)]
    public $search = '';

    #[Url(history:true)]
    public $perPage = 10;

    #[Url(history:true)]
    public $sortBy = 'created_at';

    #[Url(history:true)]
    public $sortDir = 'DESC';

    public function setSortBy($sortByField)
    {
        if ($this->sortBy === $sortByField) {
            $this->sortDir = ($this->sortDir == "ASC") ? 'DESC' : "ASC";
            return;
        }

        $this->sortBy = $sortByField;
        $this->sortDir = 'DESC';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function suppliers()
    {
        // Retrieve the suppliers, eager load products count, and latest delivery
        return Supplier::withCount('products')
            ->with(['latestDelivery']) // Eager load the latest delivery relationship
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDir) // Sort by other columns first
            ->paginate($this->perPage);
    }
}
