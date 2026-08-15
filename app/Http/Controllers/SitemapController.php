<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        return response()->view('sitemap', ['urls' => [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('privacy'), 'priority' => '0.3'],
            ['loc' => route('terms'), 'priority' => '0.3'],
        ]])->header('Content-Type', 'application/xml');
    }
}
