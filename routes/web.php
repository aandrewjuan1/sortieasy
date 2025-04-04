<?php

use App\Livewire\Dashboard\AlertSummary;
use App\Livewire\Dashboard\ProductSummary;
use App\Livewire\Dashboard\RecentActivity;
use App\Livewire\Dashboard\SupplierOverview;
use App\Livewire\Dashboard\TransactionSummary;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'dashboard');


Route::get('dashboard/product-summary', ProductSummary::class)->name('dashboard.product-summary');
Route::get('dashboard/supplier-overview', SupplierOverview::class)->name('dashboard.supplier-overview');
Route::get('dashboard/transaction-summary', TransactionSummary::class)->name('dashboard.transaction-summary');
Route::get('dashboard/alert-summary', AlertSummary::class)->name('dashboard.alert-summary');
Route::get('dashboard/recent-activity', RecentActivity::class)->name('dashboard.recent-activity');

Route::middleware(['auth'])->group(function () {
    Route::redirect('/dashboard', 'dashboard/product-summary')->name('dashboard');
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
