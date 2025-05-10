<?php

namespace App\Livewire\Dashboard;

use App\Models\Sale;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

#[Title('Dashboard')]
class SaleSummary extends Component
{
    public int $daysToShow = 30;
    public int $recentSalesLimit = 10;

    // Total sales volume (sum of all sales counts across all channels)
    #[Computed]
    public function totalVolume(): int
    {
        return array_sum(array_column($this->salesByChannel, 'count'));
    }

    // New computed property for chart data
    #[Computed]
    public function chartData(): array
    {
        $salesByChannel = $this->salesByChannel;

        // Transform the channel names for display
        $labels = array_map(function($channel) {
            return match($channel) {
                'in_store' => 'In-Store',
                'online' => 'Online',
                'phone' => 'Phone',
            };
        }, array_keys($salesByChannel));

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Sales Count',
                    'data' => array_column($salesByChannel, 'count'),
                    'backgroundColor' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    'borderColor' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Revenue',
                    'data' => array_column($salesByChannel, 'revenue'),
                    'backgroundColor' => ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
                    'borderColor' => ['#2e59d9', '#17a673', '#2c9faf', '#dda20a'],
                    'borderWidth' => 1,
                    'type' => 'bar'
                ]
            ]
        ];
    }

    // Retrieve recent sales data
    #[Computed]
    public function recentSales(): Collection
    {
        return Sale::with(['product:id,name', 'user:id,name']) // Eager load product and user
            ->where('created_at', '>=', now()->subDays($this->daysToShow))
            ->latest()
            ->limit($this->recentSalesLimit)
            ->get()
            ->map(function ($sale) {
                $sale->formatted_total = number_format($sale->quantity * $sale->unit_price, 2);
                $sale->formatted_date = $sale->created_at->format('M d, Y');
                return $sale;
            });
    }

    // Sales data grouped by channel, uses caching for 1 hour
    #[Computed()]
    public function salesByChannel(): array
    {
        $startDate = now()->subDays($this->daysToShow);

        // Get all sales in the date range and group by channel
        $sales = Sale::where('created_at', '>=', $startDate)
            ->get()
            ->groupBy('channel')
            ->map(function ($channelSales) {
                return [
                    'count' => $channelSales->count(),
                    'revenue' => $channelSales->sum('total_price'), // Using the total_price accessor from the Sale model
                ];
            });

        return $sales->toArray();
    }

    // Total revenue from all channels
    #[Computed]
    public function totalRevenue(): float
    {
        return array_sum(array_column($this->salesByChannel, 'revenue'));
    }

    // Get percentage share for each channel (e.g., revenue percentage or sales percentage)
    protected function getPercentage(string $channel, string $metric = 'revenue'): float
    {
        $total = array_sum(array_column($this->salesByChannel, $metric));

        if ($total <= 0) {
            return 0;
        }

        return ($this->salesByChannel[$channel][$metric] / $total) * 100;
    }

    public function downloadPdf()
    {
        $data = [
            'totalVolume' => $this->totalVolume,
            'totalRevenue' => $this->totalRevenue,
            'salesByChannel' => $this->salesByChannel,
            'recentSales' => $this->recentSales,
        ];

        $pdf = PDF::loadView('pdf.sale-summary', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'sale-summary.pdf');
    }
}
