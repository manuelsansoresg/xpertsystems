<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table): void {
            $table->decimal('minimum_amount', 12, 2)->nullable()->after('uses_count');
            $table->decimal('maximum_discount', 12, 2)->nullable()->after('minimum_amount');
            $table->foreignId('created_by')->nullable()->after('maximum_discount')->constrained('users')->nullOnDelete();

            $table->renameColumn('max_uses', 'usage_limit');
            $table->renameColumn('uses_per_customer', 'usage_limit_per_customer');
            $table->renameColumn('uses_count', 'times_used');
            $table->renameColumn('active', 'is_active');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table): void {
            $table->renameColumn('usage_limit', 'max_uses');
            $table->renameColumn('usage_limit_per_customer', 'uses_per_customer');
            $table->renameColumn('times_used', 'uses_count');
            $table->renameColumn('is_active', 'active');

            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['minimum_amount', 'maximum_discount']);
        });
    }
};
