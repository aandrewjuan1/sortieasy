<?php

namespace App\Livewire;

use App\Enums\AuditAction;
use App\Models\AuditLog;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;

#[Title('Audit Logs')]
class AuditLogs extends Component
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
    public $userFilter = '';

    #[Url(history: true)]
    public $actionFilter = '';

    #[Url(history: true)]
    public $tableFilter = '';

    #[Url(history: true)]
    public $dateFrom = '';

    #[Url(history: true)]
    public $dateTo = '';

    public function setSortBy($sortByField)
    {
        $isSameSortColumn = $this->sortBy === $sortByField;
        $this->sortBy = $sortByField;
        $this->sortDir = $isSameSortColumn ? ($this->sortDir == "ASC" ? 'DESC' : 'ASC') : 'DESC';
        $this->clearCurrentPageCache();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'userFilter', 'actionFilter', 'tableFilter', 'dateFrom', 'dateTo', 'perPage'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'userFilter',
            'actionFilter',
            'tableFilter',
            'dateFrom',
            'dateTo',
            'perPage',
            'sortBy',
            'sortDir',
        ]);
        $this->resetPage();
        $this->clearCurrentPageCache();
    }

    #[Computed()]
    public function users()
    {
        return Cache::remember('audit-log-users', now()->addHour(), fn() =>
            User::whereHas('auditLogs')
                ->orderBy('name')
                ->pluck('name', 'id')
        );
    }

    #[Computed()]
    public function availableActions()
    {
        return collect(AuditAction::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()]);
    }

    #[Computed()]
    public function availableTables()
    {
        return Cache::remember('audit-log-tables', now()->addHour(), fn() =>
            AuditLog::distinct('table_name')
                ->orderBy('table_name')
                ->pluck('table_name')
        );
    }

    #[Computed()]
    public function logs()
    {
        $cacheKey = $this->getLogsCacheKey();

        return Cache::remember($cacheKey, now()->addMinutes(30), fn() =>
            AuditLog::with('user')
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('description', 'like', '%' . $this->search . '%')
                          ->orWhere('record_id', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->userFilter, fn($q) => $q->where('user_id', $this->userFilter))
                ->when($this->actionFilter, fn($q) => $q->where('action', $this->actionFilter))
                ->when($this->tableFilter, fn($q) => $q->where('table_name', $this->tableFilter))
                ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
                ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
                ->orderBy($this->sortBy, $this->sortDir)
                ->paginate($this->perPage)
        );
    }

    protected function getLogsCacheKey(): string
    {
        return sprintf(
            'audit-logs:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:user:%s:action:%s:table:%s:from:%s:to:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->userFilter,
            $this->actionFilter,
            $this->tableFilter,
            $this->dateFrom,
            $this->dateTo
        );
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getLogsCacheKey());
    }

    #[On('audit-log-created')]
    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }
}
