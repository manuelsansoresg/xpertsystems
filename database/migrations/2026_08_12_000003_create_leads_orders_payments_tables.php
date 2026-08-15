<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('whatsapp', 30);
            $table->string('country', 2);
            $table->string('business_name');
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source')->default('website');
            $table->text('message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('package_id')->constrained()->restrictOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending')->index();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_whatsapp', 30);
            $table->string('country', 2);
            $table->string('business_name');
            $table->char('currency', 3);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2);
            $table->decimal('balance_amount', 10, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('provider', ['mercado_pago', 'paypal']);
            $table->string('provider_reference')->nullable()->index();
            $table->string('status')->default('pending')->index();
            $table->char('currency', 3);
            $table->decimal('amount', 10, 2);
            $table->text('checkout_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('leads');
    }
};
