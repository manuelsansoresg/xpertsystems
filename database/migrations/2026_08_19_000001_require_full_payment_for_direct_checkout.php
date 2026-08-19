<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('packages')
            ->where('direct_checkout', true)
            ->update(['deposit_percentage' => 100]);

        DB::table('settings')
            ->where('key', 'default_deposit_percentage')
            ->update(['value' => '100']);

        DB::table('orders')
            ->where('status', 'deposit_paid')
            ->update(['status' => 'paid']);
    }

    public function down(): void
    {
        // No se restaura el cobro parcial porque el pago total es una regla comercial.
    }
};
