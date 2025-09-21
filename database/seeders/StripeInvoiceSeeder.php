<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StripeInvoice;
use Illuminate\Database\Seeder;

class StripeInvoiceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    /** ---------------------------------------------------------
     * 1. Ensure we have a test user to own the invoice
     * -------------------------------------------------------- */
    $user = User::firstOrCreate(
      ['email' => 'test@example.com'],
      [
        'name'                  => 'Test User',
        'password'              => bcrypt('password'),  // login if you want
        'stripe_customer_id'    => 'cus_test123',       // fake but unique
      ]
    );

    /** ---------------------------------------------------------
     * 2. Insert one realistic invoice row
     * -------------------------------------------------------- */
    StripeInvoice::create([
      'user_id'            => $user->id,
      'stripe_invoice_id'  => 'in_test_' . uniqid(),
      'status'             => 'paid',
      'amount_due'         => 1299,       // cents
      'amount_paid'        => 1299,
      'currency'           => 'usd',
      'hosted_invoice_url' => 'https://billing.stripe.com/i/test_invoice',
      'invoice_pdf'        => null,       // Stripe may supply this later
      'stripe_created_at'  => now()->subDay(),
    ]);
  }
}
