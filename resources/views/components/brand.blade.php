@props(['light' => true, 'compact' => false])
<span {{ $attributes->class(['brand', 'brand--dark' => ! $light, 'brand--compact' => $compact]) }} aria-label="XpertSystems">
    <img src="{{ asset('images/logo-site.png') }}" width="700" height="318" alt="XpertSystems" decoding="async">
</span>
