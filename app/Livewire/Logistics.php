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

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $statusFilter = '';

    #[Url(history: true)]
    public $sortBy = 'delivery_date';

    #[Url(history: true)]
    public $sortDir = 'DESC';

    #[Computed]
    public function statuses()
    {
        return [
            'pending' => 'Pending',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
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
        if (in_array($property, ['search', 'statusFilter', 'perPage'])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function logistics()
    {
        return Logistic::with(['product'])
            ->join('products', 'logistics.product_id', '=', 'products.id')
            ->select('logistics.*') // Select all columns from logistics table
            ->search($this->search)
            ->ofStatus($this->statusFilter)
            ->when($this->sortBy === 'product.name', function ($query) {
                $query->orderBy('products.name', $this->sortDir);
            })
            ->when($this->sortBy !== 'product.name', function ($query) {
                $query->orderBy($this->sortBy, $this->sortDir);
            })
            ->paginate($this->perPage);
    }

    // Add this method to your Logistics component
    public function getTimeStatus(Logistic $logistic): array
    {
        if ($logistic->status === \App\Enums\LogisticStatus::Delivered) {
            return [
                'display' => 'Delivered',
                'class' => 'text-gray-600 dark:text-gray-300'
            ];
        }

        $diff = now()->diff($logistic->delivery_date);
        $isPast = now() > $logistic->delivery_date;
        $days = $diff->d;
        $hours = $diff->h;

        if ($days == 0 && $hours == 0) {
            return [
                'display' => 'Due now',
                'class' => 'text-yellow-600 dark:text-yellow-400'
            ];
        } elseif (!$isPast) {
            return [
                'display' => ($days > 0 ? $days . ' day' . ($days > 1 ? 's' : '') . ' ' : '') .
                            ($hours > 0 ? $hours . ' hour' . ($hours > 1 ? 's' : '') : '') .
                            ' remaining',
                'class' => 'text-green-600 dark:text-green-400'
            ];
        } else {
            return [
                'display' => ($days > 0 ? $days . ' day' . ($days > 1 ? 's' : '') . ' ' : '') .
                            ($hours > 0 ? $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ' : '') .
                            'overdue',
                'class' => 'text-red-600 dark:text-red-400'
            ];
        }
    }

    public function render()
    {
        return view('livewire.logistics');
    }
}
