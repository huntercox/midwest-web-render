<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
  /**
   * Display the login view.
   */
  public function create(): Response
  {
    Log::info('Login page requested', [
      'ip' => request()->ip(),
      'user_agent' => request()->userAgent()
    ]);

    // ▶︎ point to your Vue page under resources/js/Pages/Login.vue
    return Inertia::render('Login');
    //
    // If you placed it in resources/js/Pages/Auth/Login.vue instead, do:
    // return Inertia::render('Auth/Login');
  }

  /**
   * Handle an incoming authentication request.
   */
  public function store(LoginRequest $request): RedirectResponse
  {
    Log::info('Login request received', [
      'email' => $request->get('email'),
      'ip' => $request->ip(),
      'user_agent' => $request->userAgent(),
      'remember' => $request->boolean('remember'),
      'request_data' => $request->all()
    ]);

    try {
      $request->authenticate();

      Log::info('Authentication successful', [
        'email' => $request->get('email'),
        'ip' => $request->ip()
      ]);

      $request->session()->regenerate();

      Log::info('Redirecting to dashboard after successful login');

      return redirect()->intended(route('dashboard', absolute: false));
    } catch (\Exception $e) {
      Log::error('Login process failed', [
        'email' => $request->get('email'),
        'ip' => $request->ip(),
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      throw $e;
    }
  }

  /**
   * Destroy an authenticated session.
   */
  public function destroy(Request $request): RedirectResponse
  {
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
  }
}
