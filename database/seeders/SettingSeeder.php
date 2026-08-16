<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['key' => 'whatsapp_number', 'value' => env('WHATSAPP_NUMBER', ''), 'group' => 'contact'],
            ['key' => 'contact_email', 'value' => env('CONTACT_EMAIL', ''), 'group' => 'contact'],
            ['key' => 'default_commission_percentage', 'value' => '20', 'type' => 'decimal', 'group' => 'commissions'],
            ['key' => 'referral_attribution_days', 'value' => '30', 'type' => 'integer', 'group' => 'commercial'],
            ['key' => 'base_currency', 'value' => 'MXN', 'type' => 'string', 'group' => 'commercial'],
            ['key' => 'default_deposit_percentage', 'value' => '50', 'type' => 'integer', 'group' => 'commercial'],
            ['key' => 'renewal_reminder_days', 'value' => '[30,15,7,0]', 'type' => 'json', 'group' => 'renewals'],
        ] as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['type' => 'string', ...$setting],
            );
        }
    }
}
