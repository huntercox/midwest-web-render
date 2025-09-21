<?php

namespace App\Http\Controllers;

use App\Models\StripeInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
  public function __invoke(Request $request): Response
  {
    // 1) Verify signature
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $secret = config('services.stripe.webhook_secret');

    try {
      $event = Webhook::constructEvent($payload, $sigHeader, $secret);
    } catch (\Throwable $e) {
      report($e);
      return response('Invalid signature', 400);
    }

    // 2) React only to invoice.* events
    if (! str_starts_with($event->type, 'invoice.')) {
      return response('Event ignored', 200);
    }

    $invoice = $event->data->object; // \Stripe\Invoice

    // 3) Resolve the owning user (several ways ↓)
    $user = User::where('stripe_customer_id', $invoice->customer)->first();
    if (! $user) {
      return response('User not found', 200); // avoid 4xx so Stripe doesn’t retry forever
    }

    // 4) Upsert local copy
    StripeInvoice::updateOrCreate(
      ['stripe_invoice_id' => $invoice->id],
      [
        'user_id' => $user->id,
        'status' => $invoice->status,
        'amount_due' => $invoice->amount_due,
        'amount_paid' => $invoice->amount_paid,
        'currency' => $invoice->currency,
        'hosted_invoice_url' => $invoice->hosted_invoice_url,
        'invoice_pdf' => $invoice->invoice_pdf,
        'stripe_created_at' => \Carbon\Carbon::createFromTimestamp($invoice->created),
      ]
    );

    return response('Webhook handled', 200);
  }

  public function handle(Request $request)
  {
    Log::info('🔔 Stripe Webhook Hit', [
      'headers' => $request->headers->all(),
      'body' => $request->getContent(),
    ]);

    return response('OK', 200);
  }
}
