<?php

namespace App\Livewire\Dashboard;

use App\Models\Supplier;
use App\Models\Logistic;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

#[Title('Dashboard')]
class SupplierOverview extends Component
{
    #[Computed]
    public function totalSuppliers(): int
    {
        return Supplier::count();
    }

    #[Computed]
    public function recentDeliveries()
    {
        return Logistic::with(['product.supplier'])
            ->where('status', 'delivered')
            ->where('delivery_date', '>=', now()->subMonth())
            ->latest('delivery_date')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function topSuppliers()
    {
        return Supplier::withCount('products')
            ->orderByDesc('products_count')
            ->limit(5)
            ->get();
    }

    public function downloadPdf()
    {
        try {
            $data = [
                'totalSuppliers' => $this->totalSuppliers,
                'recentDeliveries' => $this->recentDeliveries,
                'topSuppliers' => $this->topSuppliers,
            ];

            $pdf = PDF::loadView('pdf.supplier-overview', $data);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'supplier-overview.pdf');
        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Unable to generate PDF. Please try again later.'
            );
            Log::error('PDF Generation Error: ' . $e->getMessage());
        }
    }
}
