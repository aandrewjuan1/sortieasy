<?php

namespace App\Livewire\Dashboard;

use App\Enums\LogisticStatus;
use App\Models\Logistic;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

#[Title('Dashboard')]
class LogisticSummary extends Component
{
    public int $daysToShow = 30;
    public int $upcomingDeliveriesLimit = 10;

    // Total shipments count
    #[Computed]
    public function totalShipments(): int
    {
        return Logistic::where('delivery_date', '>=', now()->subDays($this->daysToShow))->count();
    }

    // Upcoming deliveries (next 7 days)
    #[Computed]
    public function upcomingDeliveries(): Collection
    {
        return Logistic::with(['product:id,name'])
            ->whereBetween('delivery_date', [now(), now()->addDays(7)])
            ->orderBy('delivery_date')
            ->limit($this->upcomingDeliveriesLimit)
            ->get()
            ->map(function ($logistic) {
                $logistic->formatted_date = $logistic->delivery_date->format('M d, Y');
                $logistic->days_until = $logistic->delivery_date->diffInDays(now());
                return $logistic;
            });
    }

    // Shipments by status
    #[Computed]
    public function shipmentsByStatus(): array
    {
        return Logistic::where('delivery_date', '>=', now()->subDays($this->daysToShow))
            ->get()
            ->groupBy('status')
            ->map(function ($statusGroup) {
                return [
                    'count' => $statusGroup->count(),
                    'percentage' => $this->totalShipments > 0
                        ? ($statusGroup->count() / $this->totalShipments) * 100
                        : 0,
                ];
            })
            ->toArray();
    }

    // Late shipments (past delivery date not delivered)
    #[Computed]
    public function lateShipments(): Collection
    {
        return Logistic::with(['product:id,name'])
            ->where('delivery_date', '<', now())
            ->where('status', '!=', LogisticStatus::Delivered->value)
            ->orderBy('delivery_date')
            ->limit(5)
            ->get()
            ->map(function ($logistic) {
                $logistic->days_late = now()->diffInDays($logistic->delivery_date);
                return $logistic;
            });
    }

    public function downloadPdf()
    {
        try {
            $data = [
                'totalShipments' => $this->totalShipments,
                'upcomingDeliveries' => $this->upcomingDeliveries,
                'shipmentsByStatus' => $this->shipmentsByStatus,
                'lateShipments' => $this->lateShipments,
            ];

            $pdf = PDF::loadView('pdf.logistic-summary', $data);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'logistic-summary.pdf');
        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Unable to generate PDF. Please try again later.'
            );
            Log::error('PDF Generation Error: ' . $e->getMessage());
        }
    }
}
