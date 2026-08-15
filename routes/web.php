<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\QuoteController;
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
