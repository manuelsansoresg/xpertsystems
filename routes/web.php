<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\CommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\PayoutController as AdminPayoutController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::get('/contratar/{package:slug}', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/contratar/{package:slug}', [CheckoutController::class, 'store'])
    ->middleware('throttle:6,1')->name('checkout.store');
Route::get('/pago/confirmacion/{order}', [CheckoutController::class, 'returned'])->name('checkout.return');
Route::post('/cotizar/{package:slug}', [QuoteController::class, 'store'])
    ->middleware('throttle:5,1')->name('quote.store');

Route::post('/webhooks/mercado-pago', [WebhookController::class, 'mercadoPago'])
    ->middleware('throttle:120,1')->name('webhooks.mercado-pago');
Route::post('/webhooks/paypal', [WebhookController::class, 'paypal'])
    ->middleware('throttle:120,1')->name('webhooks.paypal');

Route::view('/aviso-de-privacidad', 'legal.privacy')->name('privacy');
Route::view('/terminos', 'legal.terms')->name('terms');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    Route::middleware(['auth', 'internal'])->group(function (): void {
        Route::redirect('/', '/admin/dashboard');
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::middleware('role:admin')->group(function (): void {
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/toggle', [AdminUserController::class, 'toggleActive'])->name('users.toggle');

            Route::get('/sellers', [AdminSellerController::class, 'index'])->name('sellers.index');
            Route::get('/sellers/{seller}', [AdminSellerController::class, 'show'])->name('sellers.show');
            Route::get('/sellers/{seller}/edit', [AdminSellerController::class, 'edit'])->name('sellers.edit');
            Route::put('/sellers/{seller}', [AdminSellerController::class, 'update'])->name('sellers.update');
            Route::patch('/sellers/{seller}/toggle', [AdminSellerController::class, 'toggleActive'])->name('sellers.toggle');
            Route::post('/sellers/generate-code', [AdminSellerController::class, 'generateCode'])->name('sellers.generate-code');

            Route::get('/commissions', [AdminCommissionController::class, 'index'])->name('commissions.index');
            Route::get('/commissions/{commission}', [AdminCommissionController::class, 'show'])->name('commissions.show');

            Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
            Route::get('/payouts/{payout}', [AdminPayoutController::class, 'show'])->name('payouts.show');

            Route::get('/packages', [AdminPackageController::class, 'index'])->name('packages.index');
            Route::get('/packages/create', [AdminPackageController::class, 'create'])->name('packages.create');
            Route::post('/packages', [AdminPackageController::class, 'store'])->name('packages.store');
            Route::get('/packages/{package}/edit', [AdminPackageController::class, 'edit'])->name('packages.edit');
            Route::put('/packages/{package}', [AdminPackageController::class, 'update'])->name('packages.update');
            Route::patch('/packages/{package}/toggle', [AdminPackageController::class, 'toggleActive'])->name('packages.toggle');
        });
    });
});

Route::prefix('seller')->name('seller.')->middleware(['auth', 'internal', 'role:seller'])->group(function (): void {
    Route::get('/', SellerDashboardController::class)->name('dashboard');
});
