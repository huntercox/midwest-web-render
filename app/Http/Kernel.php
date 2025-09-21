<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
	protected $middleware = [ /* … */];

	// app/Http/Kernel.php

	// app/Http/Kernel.php

	protected $middlewareGroups = [
		'web' => [
			// Encrypt cookies
			\Illuminate\Cookie\Middleware\EncryptCookies::class,

			// Add queued cookies to the response
			\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,

			// Boot up the session (⇨ makes $request->session() and $request->user() available)
			\Illuminate\Session\Middleware\StartSession::class,

			// Share validation errors and old input with the views
			\Illuminate\View\Middleware\ShareErrorsFromSession::class,

			// CSRF protection
			\App\Http\Middleware\VerifyCsrfToken::class,

			// Route model binding, etc.
			\Illuminate\Routing\Middleware\SubstituteBindings::class,

			// Finally, Inertia’s own share middleware
			\App\Http\Middleware\HandleInertiaRequests::class,
		],

		'api' => [
			'throttle:api',
			\Illuminate\Routing\Middleware\SubstituteBindings::class,
		],
	];

	protected $routeMiddleware = [ /* … */];
}
