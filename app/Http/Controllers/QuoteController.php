<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;

class QuoteController extends Controller
{
    public function store(QuoteRequest $request, Package $package): RedirectResponse
    {
        abort_unless($package->active && $package->requires_quote, 404);
        $data = $request->validated();

        Lead::create([
            ...collect($data)->except('website')->all(),
            'country' => 'MX', 'package_id' => $package->id, 'source' => 'store_quote',
        ]);

        $stored = Setting::query()->where('key', 'whatsapp_number')->value('value');
        $number = preg_replace('/\D+/', '', (string) ($stored ?: config('xpertsystems.whatsapp_number')));

        $business = trim($data['business_name'] ?? '');
        $from = $business ? "Soy {$data['name']} de {$business}" : "Soy {$data['name']}";
        $message = rawurlencode("Hola, vi el paquete Tienda en Línea de XpertSystems. {$from} y quisiera cotizar mi tienda. Me gustaría recibir información sobre el alcance del proyecto.");

        return $number
            ? redirect()->away("https://wa.me/{$number}?text={$message}")
            : back()->with('quote_success', 'Recibimos tus datos. Te contactaremos para revisar tu tienda.');
    }
}
