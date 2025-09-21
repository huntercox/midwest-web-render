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
