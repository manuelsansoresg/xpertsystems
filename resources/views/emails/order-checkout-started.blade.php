<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Datos de tu compra</title></head>
<body style="margin:0;background:#f5f1e8;color:#102f40;font-family:Arial,sans-serif">
    <div style="max-width:620px;margin:0 auto;padding:40px 24px">
        <div style="background:#ffffff;padding:32px;border:1px solid #ddd6c8">
            <h1 style="margin-top:0;font-size:26px">Tu solicitud de compra está lista</h1>
            <p>Hola, {{ $order->customer_name }}.</p>
            <p>Registramos tus datos. Estos son los detalles antes de completar el pago seguro:</p>
            <table style="width:100%;border-collapse:collapse;margin:24px 0">
                <tr><td style="padding:10px 0;border-bottom:1px solid #e5e0d7">Referencia</td><td style="padding:10px 0;border-bottom:1px solid #e5e0d7;text-align:right"><strong>{{ $order->reference }}</strong></td></tr>
                <tr><td style="padding:10px 0;border-bottom:1px solid #e5e0d7">Paquete</td><td style="padding:10px 0;border-bottom:1px solid #e5e0d7;text-align:right"><strong>{{ $order->package->name }}</strong></td></tr>
                <tr><td style="padding:10px 0;border-bottom:1px solid #e5e0d7">Importe</td><td style="padding:10px 0;border-bottom:1px solid #e5e0d7;text-align:right"><strong>${{ number_format((float) $order->total_amount, 2) }} {{ $order->currency }}</strong></td></tr>
                <tr><td style="padding:10px 0">Estado</td><td style="padding:10px 0;text-align:right"><strong>Pago pendiente</strong></td></tr>
            </table>
            <p style="margin:28px 0"><a href="{{ $checkoutUrl }}" style="display:inline-block;background:#dcb96e;color:#102f40;text-decoration:none;padding:14px 22px;font-weight:bold">Continuar a Mercado Pago</a></p>
            <p style="font-size:13px;color:#5e6b72">La contratación se confirma cuando Mercado Pago aprueba el pago.</p>
        </div>
    </div>
</body>
</html>
