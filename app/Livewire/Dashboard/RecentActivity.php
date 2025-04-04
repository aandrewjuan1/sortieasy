<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Support\Collection;

class RecentActivity extends Component
{
    #[Url(except: 'week')]
    public string $period = 'week';

    public int $limit = 5;

    #[Computed]
    public function dateRange(): Carbon
    {
        return match ($this->period) {
            'week' => Carbon::now()->subWeek()->startOfDay(),
            'month' => Carbon::now()->subMonth()->startOfDay(),
            'year' => Carbon::now()->subYear()->startOfDay(),
            default => throw new \InvalidArgumentException('Invalid period specified'),
        };
    }

    #[Computed]
    public function recentTransactions(): Collection
    {
        return Transaction::with([
            'product' => fn($query) => $query->select('id', 'name'),
            'user' => fn($query) => $query->select('id', 'name'),
        ])
            ->where('created_at', '>=', $this->dateRange)
            ->latest()
            ->take($this->limit)
            ->get()
            ->map(function ($transaction) {
                $transaction->created_at_for_humans = $transaction->created_at->diffForHumans();
                $transaction->formatted_quantity = number_format($transaction->quantity);
                return $transaction;
            });
    }
}
