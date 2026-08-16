# Panel administrativo de XpertSystems

## Primera entrega

Esta fase instala los cimientos del sistema comercial:

- autenticación interna en `/admin/login`;
- roles `admin` y `seller` sin registro público;
- middleware para cuentas internas activas y permisos por rol;
- perfil comercial de vendedor con código de referido y configuración de comisión;
- esquema base para clientes, cupones, referidos, ventas, pagos, comisiones, payouts, renovaciones, auditoría y notificaciones;
- layout responsive y dashboard inicial para administrador y vendedor.

Los CRUD operativos se activarán por módulo en las siguientes fases. Los elementos deshabilitados del menú muestran ese alcance de forma explícita.

## Decisiones de arquitectura

### Ventas

La tabla existente `orders` funciona como la entidad de venta. Esto conserva las referencias y los webhooks de Mercado Pago y PayPal. Se amplió con:

- cliente, vendedor, cupón y referido;
- snapshot del paquete y sus características;
- subtotal, descuento y total;
- estado comercial separado de `payment_status`;
- origen y notas internas.

Los precios históricos nunca dependen del precio actual del paquete.

### Comisiones

Las reglas futuras respetarán esta prioridad:

1. vendedor + paquete;
2. vendedor;
3. paquete;
4. configuración global.

Cada comisión conserva tipo, valor y base de cálculo como snapshot. La tabla permite una comisión por pago confirmado para soportar anticipos proporcionales e idempotencia.

### Atribución

La ventana inicial es de 30 días y se controla con `referral_attribution_days` en `settings`. Cuando una cookie de referido y un cupón asignado pertenecen a vendedores distintos, el cupón del vendedor tendrá prioridad. Esta regla se implementará en la fase de referidos/cupones.

### Historial financiero

Ventas, pagos, comisiones, payouts y movimientos del ledger no usan eliminación física. Los usuarios, vendedores, clientes, paquetes y cupones admiten desactivación o soft delete.

## Crear el administrador inicial

Define estas variables en `.env`:

```dotenv
ADMIN_NAME="Administrador XpertSystems"
ADMIN_EMAIL="tu-correo@dominio.com"
ADMIN_PASSWORD="una-contraseña-segura"
```

Después ejecuta:

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AdminUserSeeder
```

El seeder es idempotente: actualiza la cuenta configurada y asegura que tenga el rol `admin`. No contiene credenciales predeterminadas en el código ni en `.env.example`.

## Seguridad incluida

- contraseñas con cast `hashed` de Laravel;
- CSRF en formularios internos;
- regeneración de sesión después del login;
- limitación de cinco intentos de login por minuto;
- rechazo de cuentas inactivas o sin rol interno;
- rutas protegidas por `auth`, `internal` y `role`;
- datos de pago del vendedor cifrados;
- panel excluido de indexación con `noindex,nofollow`.

## Siguiente fase

La fase 2 convierte la administración de paquetes en un módulo completo: listado, alta, edición, características ordenables, visibilidad pública y sincronización inmediata con la landing.
