<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table): void {
            $table->text('long_description')->nullable()->after('short_description');
            $table->decimal('renewal_price', 10, 2)->nullable()->after('deposit_percentage');
            $table->string('renewal_period', 30)->nullable()->after('renewal_price');
            $table->boolean('renewal_required')->default(false)->after('renewal_period');
            $table->string('button_text')->nullable()->after('badge');
            $table->boolean('public_visibility')->default(true)->after('active');
            $table->softDeletes();
        });

        Schema::create('package_features', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('visible_summary')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['package_id', 'active', 'sort_order']);
        });

        Schema::create('commission_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('seller_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('commission_type', 20)->default('percentage');
            $table->decimal('commission_value', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['seller_id', 'package_id', 'active']);
        });

        DB::table('packages')->orderBy('id')->each(function (object $package): void {
            $features = json_decode((string) $package->features, true);

            foreach (is_array($features) ? $features : [] as $index => $feature) {
                DB::table('package_features')->insert([
                    'package_id' => $package->id,
                    'title' => $feature,
                    'visible_summary' => $index < 6,
                    'sort_order' => $index + 1,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rules');
        Schema::dropIfExists('package_features');

        Schema::table('packages', function (Blueprint $table): void {
            $table->dropColumn([
                'long_description',
                'renewal_price',
                'renewal_period',
                'renewal_required',
                'button_text',
                'public_visibility',
                'deleted_at',
            ]);
        });
    }
};
