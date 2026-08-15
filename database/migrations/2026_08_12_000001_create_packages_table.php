<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->decimal('price', 10, 2);
            $table->char('currency', 3)->default('MXN');
            $table->enum('price_type', ['fixed', 'starting_at'])->default('fixed');
            $table->boolean('direct_checkout')->default(false);
            $table->boolean('requires_quote')->default(false);
            $table->unsignedTinyInteger('deposit_percentage')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('badge')->nullable();
            $table->json('features');
            $table->text('note')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
