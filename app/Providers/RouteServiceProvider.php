<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
  public function map(): void
  {
    $this->mapWebRoutes();
    $this->mapApiRoutes();
  }

  protected function mapWebRoutes(): void
  {
    Route::middleware('web')
      ->namespace($this->namespace)
      ->group(base_path('routes/web.php'));
  }

  protected function mapApiRoutes(): void
  {
    Route::prefix('api')
      ->middleware('api')
      ->namespace($this->namespace)
      ->group(base_path('routes/api.php'));
  }
}
