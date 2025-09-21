<?php

namespace App\Providers;

use Stripe\Stripe;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    // DEBUG: Test route bypassing web.php
    Route::get('/service-provider-test', function () {
      return response()->json([
        'message' => 'SUCCESS! Route registered in AppServiceProvider',
        'timestamp' => now()->toISOString(),
        'routes_loaded' => count(app('router')->getRoutes()),
        'environment' => app()->environment(),
      ]);
    });

    // Force HTTPS for assets in production
    if (config('app.env') === 'production') {
      URL::forceScheme('https');
    }

    Stripe::setApiKey(config('services.stripe.secret'));

    Inertia::share([
      'auth' => [
        'user' => fn() => Auth::user()
      ],
    ]);
  }
}
