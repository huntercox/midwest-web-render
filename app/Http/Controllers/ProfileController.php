<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
  /**
   * Display the user's profile form.
   */
  public function edit(Request $request)
  {
    return Inertia::render('Profile/Edit', [
      'user' => $request->user(),
    ]);
  }

  /**
   * Update the user's profile information.
   */
  public function update(ProfileUpdateRequest $request): RedirectResponse
  {
    Log::info('Profile update attempt', [
      'user_id' => $request->user()->id,
      'data' => $request->validated(),
      'ip' => $request->ip(),
    ]);

    $request->user()->fill($request->validated());

    if ($request->user()->isDirty('email')) {
      $request->user()->email_verified_at = null;
    }

    $request->user()->save();

    Log::info('Profile updated successfully', [
      'user_id' => $request->user()->id,
    ]);

    return back()->with('success', 'Profile updated successfully!');
  }

  /**
   * Update the user's password.
   */
  public function updatePassword(Request $request): RedirectResponse
  {
    Log::info('Password update attempt', [
      'user_id' => $request->user()->id,
      'ip' => $request->ip(),
    ]);

    $validated = $request->validateWithBag('updatePassword', [
      'current_password' => ['required', 'current_password'],
      'password' => ['required', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
    ]);

    $request->user()->update([
      'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
    ]);

    Log::info('Password updated successfully', [
      'user_id' => $request->user()->id,
    ]);

    return back()->with('success', 'Password updated successfully!');
  }

  /**
   * Delete the user's account.
   */
  public function destroy(Request $request): RedirectResponse
  {
    $request->validateWithBag('userDeletion', [
      'password' => ['required', 'current_password'],
    ]);

    $user = $request->user();

    Auth::logout();

    $user->delete();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Redirect::to('/');
  }
}
