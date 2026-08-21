<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        return response()->view('sitemap', ['urls' => [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('paginas-web'), 'priority' => '0.9'],
            ['loc' => route('landing-pages'), 'priority' => '0.9'],
            ['loc' => route('tiendas-en-linea'), 'priority' => '0.9'],
            ['loc' => route('paginas-web-merida'), 'priority' => '0.8'],
            ['loc' => route('portafolio'), 'priority' => '0.7'],
            ['loc' => route('precios'), 'priority' => '0.8'],
            ['loc' => route('contacto'), 'priority' => '0.7'],
        ]])->header('Content-Type', 'application/xml');
    }
}
