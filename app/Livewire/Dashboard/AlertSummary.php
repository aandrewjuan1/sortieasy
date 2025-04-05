<?php

namespace App\Livewire\Dashboard;

use App\Models\Alert;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Title('Dashboard')]
class AlertSummary extends Component
{
    public bool $showResolved = false;
    public bool $isLoading = false;

    #[Computed(persist: true, seconds: 300)] // Cache for 5 minutes
    public function alerts()
    {
        return Alert::query()
            ->when(!$this->showResolved, fn($q) => $q->where('resolved', false))
            ->with('product')
            ->latest()
            ->get()
            ->groupBy('severity'); // Group by severity, not type anymore
    }

    #[Computed]
    public function criticalAlerts()
    {
        return $this->alerts()->get('critical', collect());
    }

    #[Computed]
    public function highAlerts()
    {
        return $this->alerts()->get('high', collect());
    }

    #[Computed]
    public function mediumAlerts()
    {
        return $this->alerts()->get('medium', collect());
    }

    #[Computed]
    public function lowAlerts()
    {
        return $this->alerts()->get('low', collect());
    }

    #[Computed]
    public function alertStats()
    {
        return [
            'resolved' => Alert::where('resolved', true)->count(),
            'unresolved' => Alert::where('resolved', false)->count(),
            'total' => Alert::count(),
        ];
    }

    public function toggleResolved(): void
    {
        $this->showResolved = !$this->showResolved;
    }

    #[On('alert-resolved')]
    public function refreshAlerts(): void
    {
        $this->reset('alerts');
    }

    public function render()
    {
        return view('livewire.dashboard.alert-summary');
    }
}
