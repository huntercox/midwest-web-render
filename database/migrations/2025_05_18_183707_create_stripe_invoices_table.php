<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('stripe_invoices', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();

      $table->string('stripe_invoice_id')->unique();
      $table->string('status');                   // draft | open | paid | void …
      $table->unsignedBigInteger('amount_due');
      $table->unsignedBigInteger('amount_paid');
      $table->string('currency', 3);

      $table->string('hosted_invoice_url')->nullable();
      $table->string('invoice_pdf')->nullable();

      $table->timestamp('stripe_created_at')->nullable(); // original timestamp
      $table->timestamps();                               // local created_at / updated_at
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('stripe_invoices');
  }
};
