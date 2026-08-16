<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Landing Page', 'slug' => 'landing-page',
                'short_description' => 'Ideal para promocionar tu negocio, servicio o producto y convertir visitas en contactos.',
                'price' => 2700, 'currency' => 'MXN', 'price_type' => 'fixed',
                'direct_checkout' => true, 'requires_quote' => false, 'deposit_percentage' => 50,
                'featured' => false, 'is_featured' => false,
                'button_text' => 'Contratar Landing',
                'renewal_enabled' => true, 'renewal_price' => 1200, 'renewal_period' => 'yearly',
                'renewal_after_months' => 12, 'renewal_includes' => ['domain', 'hosting', 'ssl', 'support'],
                'renewal_public_text' => 'Renovación anual: $1,200 MXN. Incluye dominio, hosting, SSL y soporte técnico.',
                'show_renewal_price' => true,
                'features' => ['Landing diseñada a medida', 'Diseño 100% responsive', 'Formulario de contacto', 'Botón de WhatsApp', 'Enlaces a redes sociales', 'SEO básico', 'Configuración técnica', 'Dominio .com durante 1 año', 'Hosting durante 1 año', 'Certificado SSL', 'Soporte técnico por problemas del sitio durante 1 año'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Página Profesional', 'slug' => 'pagina-profesional',
                'short_description' => 'Para negocios que necesitan una presencia completa, profesional y confiable en internet.',
                'price' => 4400, 'currency' => 'MXN', 'price_type' => 'fixed',
                'direct_checkout' => true, 'requires_quote' => false, 'deposit_percentage' => 50,
                'featured' => true, 'is_featured' => true, 'badge' => 'Más popular',
                'button_text' => 'Contratar Profesional',
                'renewal_enabled' => true, 'renewal_price' => 1500, 'renewal_period' => 'yearly',
                'renewal_after_months' => 12, 'renewal_includes' => ['domain', 'hosting', 'ssl', 'support'],
                'renewal_public_text' => 'Renovación anual: $1,500 MXN. Incluye dominio, hosting, SSL y soporte técnico.',
                'show_renewal_price' => true,
                'features' => ['Hasta 5 secciones/páginas principales', 'Diseño personalizado', 'Diseño 100% responsive', 'Presentación de servicios', 'Formulario de contacto', 'Botón de WhatsApp', 'Mapa de ubicación cuando aplique', 'Enlaces a redes sociales', 'SEO básico', 'Dominio .com durante 1 año', 'Hosting durante 1 año', 'Certificado SSL', 'Soporte técnico por problemas del sitio durante 1 año'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Tienda en Línea', 'slug' => 'tienda-en-linea',
                'short_description' => 'Para negocios que quieren vender productos y recibir pedidos directamente desde internet.',
                'price' => 5900, 'currency' => 'MXN', 'price_type' => 'starting_at',
                'direct_checkout' => false, 'requires_quote' => true, 'deposit_percentage' => null,
                'featured' => false, 'is_featured' => false,
                'button_text' => 'Cotizar Tienda',
                'renewal_enabled' => true, 'renewal_price' => 1800, 'renewal_period' => 'yearly',
                'renewal_after_months' => 12, 'renewal_includes' => ['domain', 'hosting', 'ssl', 'support'],
                'renewal_public_text' => 'Renovación anual: $1,800 MXN. Incluye dominio, hosting, SSL y soporte técnico.',
                'show_renewal_price' => true,
                'features' => ['WordPress + WooCommerce cuando sea la solución adecuada', 'Diseño personalizado', 'Diseño responsive', 'Catálogo administrable', 'Carrito de compras', 'Checkout', 'Pagos en línea', 'Configuración inicial de método de pago', 'Configuración básica de envíos', 'Botón de WhatsApp', 'SEO básico', 'Capacitación básica', 'Carga inicial de productos incluida', 'Administración posterior por parte del cliente', 'Dominio .com durante 1 año', 'Hosting durante 1 año', 'Certificado SSL', 'Soporte técnico por problemas del sitio durante 1 año'],
                'note' => 'Incluye carga inicial de productos. Después podrás seguir agregando más desde tu panel.',
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $package) {
            $model = Package::withTrashed()->updateOrCreate(['slug' => $package['slug']], $package);
            $model->restore();
            $titles = collect($package['features']);

            $titles->each(function (string $title, int $index) use ($model): void {
                $model->featureItems()->updateOrCreate(
                    ['title' => $title],
                    [
                        'visible_summary' => $index < 6,
                        'sort_order' => $index + 1,
                        'active' => true,
                    ],
                );
            });

            $model->featureItems()->whereNotIn('title', $titles)->delete();
        }
    }
}
