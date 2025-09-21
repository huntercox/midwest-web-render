<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
  /**
   * Create a Payment Intent and return the client secret.
   */
  public function createPaymentIntent(Request $request)
  {
    // Optionally, validate the incoming amount or other parameters here.
    $amount = 1099; // amount in cents (for example, $10.99)

    // Set Stripe API secret key from config
    Stripe::setApiKey(config('services.stripe.secret'));

    // Create a PaymentIntent with automatic payment methods enabled
    $paymentIntent = PaymentIntent::create([
      'amount' => $amount,
      'currency' => 'usd',
      'automatic_payment_methods' => [
        'enabled' => true,
      ],
    ]);

    // Return the client secret to the client (for use in the frontend)
    return response()->json([
      'clientSecret' => $paymentIntent->client_secret,
    ]);
  }
}
