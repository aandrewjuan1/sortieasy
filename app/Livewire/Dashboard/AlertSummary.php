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
            ->groupBy('type');
    }

    #[Computed]
    public function lowStockAlerts()
    {
        return $this->alerts()->get('low_stock', collect());
    }

    #[Computed]
    public function restockSuggestions()
    {
        return $this->alerts()->get('restock_suggestion', collect());
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
