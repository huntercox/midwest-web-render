<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InertiaController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Simple test route - no middleware, no Inertia
Route::get('/test', function () {
	return 'Hello from Laravel! Routing is working.';
});

// Debug route to check Laravel bootstrap and environment
Route::get('/debug', function () {
	$info = [
		'laravel_version' => app()->version(),
		'environment' => app()->environment(),
		'working_dir' => getcwd(),
		'app_path' => app_path(),
		'base_path' => base_path(),
		'routes_loaded' => count(app('router')->getRoutes()),
		'php_version' => PHP_VERSION,
		'app_debug' => config('app.debug'),
		'app_key_set' => !empty(config('app.key')),
	];

	return response()->json($info, 200, [], JSON_PRETTY_PRINT);
});

Route::middleware(['web'])->group(function () {
	Route::get('/', function () {
		return Inertia::render('Home');
	});

	Route::get('/contact', function () {
		return Inertia::render('Contact', [
			'hcaptcha_sitekey' => config('captcha.sitekey')
		]);
	})->name('contact');

	// Protected routes
	Route::middleware('auth')->group(function () {
		Route::get('/dashboard', [InertiaController::class, 'dashboard'])
			->name('dashboard');
	});

	Route::middleware('auth')->group(function () {
		Route::get('/edit-profile', [ProfileController::class, 'edit'])->name('profile.edit');
		Route::patch('/edit-profile', [ProfileController::class, 'update'])->name('profile.update');
		Route::delete('/edit-profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
		Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
	});

	Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
});

require __DIR__ . '/auth.php';
