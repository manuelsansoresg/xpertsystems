<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $settings = Setting::query()->pluck('value', 'key');

        return view('home', [
            'packages' => Package::query()->with('featureItems')->where('active', true)->orderBy('sort_order')->get(),
            'projects' => Project::query()->where('active', true)->orderBy('sort_order')->get(),
            'whatsapp' => preg_replace('/\D+/', '', $settings->get('whatsapp_number') ?: config('xpertsystems.whatsapp_number', '')),
            'contactEmail' => $settings->get('contact_email') ?: config('xpertsystems.contact_email', ''),
        ]);
    }
}
