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
        ] as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
