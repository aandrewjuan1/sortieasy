<?php

use App\Livewire\AuditLogs;
use App\Livewire\Logistics\Logistics;
use App\Livewire\ManageUsers\Users;
use App\Livewire\Transactions\Transactions;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Inventory\Products;
use App\Livewire\Settings\Appearance;
use App\Livewire\Suppliers\Suppliers;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\SaleSummary;
use App\Livewire\Dashboard\AlertSummary;
use App\Livewire\Dashboard\ProductSummary;
use App\Livewire\Dashboard\SupplierOverview;
use App\Livewire\Dashboard\TransactionSummary;
use App\Livewire\Sales\Sales;

Route::redirect('/', 'dashboard');


Route::middleware(['auth'])->group(function () {
    Route::redirect('dashboard', 'dashboard/product-summary')->name('dashboard');
    Route::redirect('settings', 'settings/profile');

    Route::get('dashboard/product-summary', ProductSummary::class)->name('dashboard.product-summary');
    Route::get('dashboard/supplier-overview', SupplierOverview::class)->name('dashboard.supplier-overview');
    Route::get('dashboard/transaction-summary', TransactionSummary::class)->name('dashboard.transaction-summary');
    Route::get('dashboard/sale-summary', SaleSummary::class)->name('dashboard.sale-summary');
    Route::get('dashboard/alert-summary', AlertSummary::class)->name('dashboard.alert-summary');

    Route::get('suppliers', Suppliers::class)->name('suppliers');
    Route::get('inventory', Products::class)->name('inventory');
    Route::get('transactions', Transactions::class)->name('transactions');
    Route::get('logistics', Logistics::class)->name('logistics');
    Route::get('sales', Sales::class)->name('sales');


    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

Route::middleware(['auth','admin'])->group(function () {
    Route::get('audit-logs', AuditLogs::class)->name('audit-logs');
    Route::get('manage-users', Users::class)->name('manage-users');
});

require __DIR__.'/auth.php';
