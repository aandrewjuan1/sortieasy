<?php

use App\Livewire\Products;
use App\Livewire\Suppliers;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
use App\Livewire\Transactions;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\AlertSummary;
use App\Livewire\Dashboard\ProductSummary;
use App\Livewire\Dashboard\RecentActivity;
use App\Livewire\Dashboard\SupplierOverview;
use App\Livewire\Dashboard\TransactionSummary;
use App\Livewire\Logistics;
use App\Livewire\ProductStock;

Route::redirect('/', 'dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('dashboard', 'dashboard/product-summary')->name('dashboard');
    Route::redirect('settings', 'settings/profile');

    Route::get('dashboard/product-summary', ProductSummary::class)->name('dashboard.product-summary');
    Route::get('dashboard/supplier-overview', SupplierOverview::class)->name('dashboard.supplier-overview');
    Route::get('dashboard/transaction-summary', TransactionSummary::class)->name('dashboard.transaction-summary');
    Route::get('dashboard/alert-summary', AlertSummary::class)->name('dashboard.alert-summary');
    Route::get('dashboard/recent-activity', RecentActivity::class)->name('dashboard.recent-activity');

    Route::get('suppliers', Suppliers::class)->name('suppliers');
    Route::get('inventory', Products::class)->name('inventory');
    Route::get('transactions', Transactions::class)->name('transactions');
    Route::get('logistics', Logistics::class)->name('logistics');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
