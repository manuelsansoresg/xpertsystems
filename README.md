# XpertSystems

Landing comercial y flujo de contratación construidos con Laravel 13, Blade, Tailwind CSS 4, Alpine.js, GSAP y Vite.

## Preparación local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## Configuración necesaria

Completa en `.env` los datos que correspondan:

- `WHATSAPP_NUMBER` y `CONTACT_EMAIL`
- `MERCADO_PAGO_ACCESS_TOKEN` y `MERCADO_PAGO_WEBHOOK_SECRET`
- `PAYPAL_CLIENT_ID`, `PAYPAL_SECRET`, `PAYPAL_WEBHOOK_ID` y `PAYPAL_MODE`
- `GA4_MEASUREMENT_ID` y `META_PIXEL_ID` (opcionales)

Las URLs de webhook son `/webhooks/mercado-pago` y `/webhooks/paypal`. Los montos se calculan desde los paquetes guardados en la base de datos; nunca se reciben desde JavaScript.

## Contenido administrable

Los paquetes y proyectos viven en `packages` y `projects`. Los datos iniciales se crean con los seeders. Las imágenes de proyecto pueden añadirse en `desktop_image` y `mobile_image`; mientras no existan, la landing muestra composiciones editoriales de respaldo.

## Verificación

```bash
php artisan test
npm run build
```
