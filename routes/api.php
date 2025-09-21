<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;


// routes/api.php  (keep web.php clean)
Route::post('/stripe/webhook', StripeWebhookController::class)
  ->name('stripe.webhook');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
