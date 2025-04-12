<?php

namespace App\Livewire\Logistics;

use Livewire\Component;
use App\Models\Logistic;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;

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
        $isSameSortColumn = $this->sortBy === $sortByField;
        $this->sortBy = $sortByField;
        $this->sortDir = $isSameSortColumn ? ($this->sortDir == "ASC" ? 'DESC' : 'ASC') : 'DESC';
        $this->clearCurrentPageCache();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'statusFilter', 'perPage'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'statusFilter',
            'perPage',
            'sortBy',
            'sortDir',
        ]);
        $this->resetPage();
        $this->clearCurrentPageCache();
    }

    #[Computed]
    public function logistics()
    {
        return Logistic::withProduct()
            ->joinProduct()
            ->search($this->search)
            ->ofStatus($this->statusFilter)
            ->sortByField($this->sortBy, $this->sortDir)
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

    protected function getLogisticCacheKey(): string
    {
        return sprintf(
            'logistics:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:status:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->statusFilter,
        );

        // logistics:page:1:per_page:10:sort:created_at:dir:DESC:search::status:
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getLogisticCacheKey());
    }

    #[On('logistic-deleted')]
    #[On('logistic-updated')]
    #[On('logistic-added')]
    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }
}
