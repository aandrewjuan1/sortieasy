<?php

namespace App\Livewire\Dashboard;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
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

    protected function getTransactionTextColor(string $type): string
    {
        return match ($type) {
            'purchase' => 'text-green-600 dark:text-green-400',
            'sale' => 'text-blue-600 dark:text-blue-400',
            'return' => 'text-yellow-600 dark:text-yellow-400',
            'adjustment' => 'text-purple-600 dark:text-purple-400',
            default => 'text-gray-600 dark:text-gray-400',
        };
    }

    protected function getTransactionBgColor(string $type): string
    {
        return match ($type) {
            'purchase' => 'bg-green-500',
            'sale' => 'bg-blue-500',
            'return' => 'bg-yellow-500',
            'adjustment' => 'bg-purple-500',
            default => 'bg-gray-500',
        };
    }

    protected function getTransactionIconBgColor(string $type): string
    {
        return match ($type) {
            'purchase' => 'bg-green-100 dark:bg-green-900/30',
            'sale' => 'bg-blue-100 dark:bg-blue-900/30',
            'return' => 'bg-yellow-100 dark:bg-yellow-900/30',
            'adjustment' => 'bg-purple-100 dark:bg-purple-900/30',
            default => 'bg-gray-100 dark:bg-gray-700',
        };
    }

    protected function getDarkModeColor(string $type): string
    {
        return match ($type) {
            'purchase' => 'bg-green-900 text-green-200',
            'sale' => 'bg-blue-900 text-blue-200',
            'return' => 'bg-yellow-900 text-yellow-200',
            'adjustment' => 'bg-purple-900 text-purple-200',
            default => 'bg-gray-700 text-gray-300',
        };
    }

    protected function getTransactionQtyColor(string $type): string
    {
        return match ($type) {
            'purchase' => 'text-green-600 dark:text-green-400',
            'sale' => 'text-blue-600 dark:text-blue-400',
            'return' => 'text-yellow-600 dark:text-yellow-400',
            'adjustment' => 'text-purple-600 dark:text-purple-400',
            default => 'text-gray-600 dark:text-gray-400',
        };
    }

    protected function getPercentage(string $type): float
    {
        $total = array_sum($this->transactionVolume);
        return $total > 0 ? (($this->transactionVolume[$type] ?? 0) / $total) * 100 : 0;
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
