<?php

namespace App\Livewire\Dashboard;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TransactionSummary extends Component
{
    public int $daysToShow = 30;
    public int $recentTransactionsLimit = 10;

    #[Computed(persist: true, seconds: 3600)]
    public function recentTransactions(): Collection
    {
        return Transaction::with(['product', 'user'])
            ->latest()
            ->limit($this->recentTransactionsLimit)
            ->get()
            ->map(function ($transaction) {
                $transaction->formatted_date = $transaction->created_at->toFormattedDateString();
                $transaction->formatted_type = $transaction->type->label();
                return $transaction;
            });
    }

    #[Computed(persist: true, seconds: 3600)]
    public function transactionVolume(): array
    {
        // Get volumes from database (returns array with string keys)
        $volumes = Transaction::selectRaw('type, SUM(quantity) as total')
            ->where('created_at', '>=', Carbon::now()->subDays($this->daysToShow))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // Initialize with all possible types set to 0
        $result = array_fill_keys(
            array_map(fn($case) => $case->value, TransactionType::cases()),
            0
        );

        // Merge with actual data (this will preserve string keys)
        foreach ($volumes as $type => $total) {
            $result[$type] = $total;
        }

        return $result;
    }

    #[Computed]
    public function transactionTypes(): array
    {
        return array_map(fn($case) => $case->value, TransactionType::cases());
    }

    public function render()
    {
        return view('livewire.dashboard.transaction-summary');
    }
}
