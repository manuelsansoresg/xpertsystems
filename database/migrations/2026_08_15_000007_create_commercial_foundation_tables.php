<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('email')->index();
            $table->string('phone', 30)->nullable();
            $table->string('country', 2)->default('MX');
            $table->char('currency', 3)->default('MXN');
            $table->string('referral_code')->nullable()->index();
            $table->string('source', 30)->default('direct');
            $table->text('notes')->nullable();
            $table->timestamp('first_purchase_at')->nullable();
            $table->timestamp('last_purchase_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('coupons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('discount_type', 20);
            $table->decimal('discount_value', 10, 2);
            $table->string('scope', 20)->default('global');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('uses_per_customer')->default(1);
            $table->unsignedInteger('uses_count')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('coupon_package', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['coupon_id', 'package_id']);
        });

        Schema::create('referrals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('referral_code')->index();
            $table->string('visitor_token')->unique();
            $table->text('landing_url')->nullable();
            $table->text('referrer_url')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamp('attributed_at');
            $table->timestamp('expires_at')->index();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('customer_id')->nullable()->after('lead_id')->constrained()->nullOnDelete();
            $table->foreignId('seller_id')->nullable()->after('customer_id')->constrained('users')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->after('seller_id')->constrained()->nullOnDelete();
            $table->foreignId('referral_id')->nullable()->after('coupon_id')->constrained()->nullOnDelete();
            $table->string('referral_code')->nullable()->after('referral_id')->index();
            $table->string('package_name_snapshot')->nullable()->after('business_name');
            $table->json('package_features_snapshot')->nullable()->after('package_name_snapshot');
            $table->decimal('subtotal_amount', 10, 2)->nullable()->after('currency');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal_amount');
            $table->string('payment_status', 30)->default('pending')->after('status')->index();
            $table->string('source', 30)->default('website')->after('payment_status')->index();
            $table->text('notes')->nullable()->after('metadata');
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->string('payment_type', 30)->default('deposit')->after('status');
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('amount');
            $table->string('external_event_id')->nullable()->unique()->after('provider_reference');
            $table->foreignId('recorded_by')->nullable()->after('order_id')->constrained('users')->nullOnDelete();
        });

        Schema::create('commissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_id')->nullable()->unique()->constrained()->restrictOnDelete();
            $table->decimal('base_amount', 10, 2);
            $table->string('commission_type_snapshot', 20);
            $table->decimal('commission_value_snapshot', 10, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->string('calculation_basis', 30)->default('after_discount');
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('earned_at')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payouts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->string('reference')->nullable()->index();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payout_commission', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payout_id')->constrained()->restrictOnDelete();
            $table->foreignId('commission_id')->unique()->constrained()->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        Schema::create('commission_ledger_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('commission_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('payout_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('type', 30);
            $table->decimal('amount', 10, 2);
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });

        Schema::create('renewals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_name');
            $table->date('renewal_date')->index();
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('MXN');
            $table->string('status', 20)->default('upcoming')->index();
            $table->json('reminder_status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 80)->index();
            $table->nullableMorphs('auditable');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('renewals');
        Schema::dropIfExists('commission_ledger_entries');
        Schema::dropIfExists('payout_commission');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('commissions');

        Schema::table('payments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('recorded_by');
            $table->dropColumn(['payment_type', 'refunded_amount', 'external_event_id']);
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('customer_id');
            $table->dropConstrainedForeignId('seller_id');
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropConstrainedForeignId('referral_id');
            $table->dropColumn([
                'referral_code',
                'package_name_snapshot',
                'package_features_snapshot',
                'subtotal_amount',
                'discount_amount',
                'payment_status',
                'source',
                'notes',
            ]);
        });

        Schema::dropIfExists('referrals');
        Schema::dropIfExists('coupon_package');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('customers');
    }
};
