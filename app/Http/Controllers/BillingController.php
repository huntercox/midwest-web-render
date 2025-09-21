<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{

  public function history()
  {
    $user     = auth()->user();
    $invoices = $user->stripeInvoices()
      ->latest('stripe_created_at')
      ->paginate(12);

    return Inertia::render('Billing/History', [
      'invoices' => $invoices,
    ]);
  }
}
