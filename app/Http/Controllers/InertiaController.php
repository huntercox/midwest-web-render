<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\StripeInvoice; // Add this line
use Illuminate\Support\Facades\Auth; // Add this line

class InertiaController extends Controller
{
  /**
   * Show the dashboard page.
   */
  public function dashboard()
  {
    $user = Auth::user(); // Get the authenticated user
    $stripeInvoices = [];

    if ($user) {
      // Assuming a User has many StripeInvoices
      $stripeInvoices = $user->stripeInvoices()->latest()->take(5)->get();
    }

    return Inertia::render('Dashboard', [
      'stripeInvoices' => $stripeInvoices,
    ]);
  }
}
