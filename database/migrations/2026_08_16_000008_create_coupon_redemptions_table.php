<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupon_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code_snapshot');
            $table->string('discount_type_snapshot');
            $table->decimal('discount_value_snapshot', 10, 2);
            $table->decimal('discount_amount', 12, 2);
            $table->timestamp('redeemed_at');
            $table->timestamps();

            $table->index(['coupon_id', 'redeemed_at']);
            $table->index('customer_id');
            $table->index('sale_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_redemptions');
    }
};
