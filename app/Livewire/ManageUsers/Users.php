<?php

namespace App\Livewire\ManageUsers;

use App\Models\User;
use App\Enums\UserRole;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;

#[Title('Users Management')]
class Users extends Component
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
    public $roleFilter = 'employee';

    #[Url(history: true)]
    public $statusFilter = '';

    public function setSortBy($sortByField)
    {
        $isSameSortColumn = $this->sortBy === $sortByField;
        $this->sortBy = $sortByField;
        $this->sortDir = $isSameSortColumn ? ($this->sortDir == "ASC" ? 'DESC' : 'ASC') : 'DESC';
        $this->clearCurrentPageCache();
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'roleFilter', 'statusFilter', 'perPage'])) {
            $this->clearCurrentPageCache();
            $this->resetPage();
        }
    }

    public function clearAllFilters()
    {
        $this->reset([
            'search',
            'roleFilter',
            'statusFilter',
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
        $cacheKey = $this->getUsersCacheKey();

        return Cache::remember($cacheKey, now()->addMinutes(30), fn() => User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role', UserRole::tryFrom($this->roleFilter));
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage));
    }

    protected function getUsersCacheKey(): string
    {
        return sprintf(
            'users:page:%d:per_page:%d:sort:%s:dir:%s:search:%s:role:%s:status:%s',
            $this->getPage(),
            $this->perPage,
            $this->sortBy,
            $this->sortDir,
            $this->search,
            $this->roleFilter,
            $this->statusFilter
        );

        // users:page:1:per_page:10:sort:created_at:dir:DESC:search::role:employee:status:
    }

    protected function clearCurrentPageCache(): void
    {
        Cache::forget($this->getUsersCacheKey());
    }

    #[On('user-added')]
    #[On('user-updated')]
    #[On('user-deleted')]
    public function clearCache()
    {
        $this->clearCurrentPageCache();
    }

    public function toggleStatus($userId)
    {
        $userToUpdate = User::findOrFail($userId);

        // Authorize the action using the policy
        $this->authorize('changeStatus', $userToUpdate);

        // Toggle the status
        $userToUpdate->update(['is_active' => !$userToUpdate->is_active]);

        // Clear cache and dispatch event
        $this->clearCurrentPageCache();
        $this->dispatch('user-status-changed');

    }
}
