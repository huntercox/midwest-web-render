<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeInvoice extends Model
{
  protected $fillable = [
    'user_id',
    'stripe_invoice_id',
    'status',
    'amount_due',
    'amount_paid',
    'currency',
    'hosted_invoice_url',
    'invoice_pdf',
    'stripe_created_at',
  ];


  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
