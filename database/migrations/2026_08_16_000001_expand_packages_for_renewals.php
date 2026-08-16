<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('featured');
            }
            if (!Schema::hasColumn('packages', 'renewal_enabled')) {
                $table->boolean('renewal_enabled')->default(false)->after('renewal_required');
            }
            if (!Schema::hasColumn('packages', 'renewal_price')) {
                $table->decimal('renewal_price', 10, 2)->nullable()->after('renewal_enabled');
            }
            if (!Schema::hasColumn('packages', 'renewal_period')) {
                $table->string('renewal_period', 20)->nullable()->after('renewal_price');
            }
            if (!Schema::hasColumn('packages', 'renewal_after_months')) {
                $table->unsignedSmallInteger('renewal_after_months')->nullable()->after('renewal_period');
            }
            if (!Schema::hasColumn('packages', 'renewal_includes')) {
                $table->json('renewal_includes')->nullable()->after('renewal_after_months');
            }
            if (!Schema::hasColumn('packages', 'renewal_public_text')) {
                $table->text('renewal_public_text')->nullable()->after('renewal_includes');
            }
            if (!Schema::hasColumn('packages', 'show_renewal_price')) {
                $table->boolean('show_renewal_price')->default(true)->after('renewal_public_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'is_featured',
                'renewal_enabled',
                'renewal_price',
                'renewal_period',
                'renewal_after_months',
                'renewal_includes',
                'renewal_public_text',
                'show_renewal_price',
            ]);
        });
    }
};
