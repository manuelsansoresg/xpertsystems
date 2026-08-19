<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Models\Package;
use App\Models\PackageFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class PackageController extends Controller
{
    public function index(): View
    {
        $packages = Package::withTrashed()
            ->with('featureItems')
            ->orderBy('sort_order')
            ->get();

        return view('admin.packages.index', [
            'packages' => $packages,
        ]);
    }

    public function create(): View
    {
        return view('admin.packages.create');
    }

    public function store(StorePackageRequest $request): RedirectResponse
    {
        $package = Package::create([
            'name' => $request->validated('name'),
            'slug' => $request->validated('slug') ?? Str::slug($request->validated('name')),
            'short_description' => $request->validated('short_description'),
            'long_description' => $request->validated('long_description'),
            'price' => $request->validated('price'),
            'currency' => $request->validated('currency', 'MXN'),
            'price_type' => $request->validated('price_type'),
            'direct_checkout' => $request->boolean('direct_checkout'),
            'requires_quote' => $request->boolean('requires_quote'),
            'deposit_percentage' => $request->boolean('direct_checkout') ? 100 : null,
            'featured' => $request->boolean('featured'),
            'is_featured' => $request->boolean('is_featured'),
            'badge' => $request->validated('badge'),
            'note' => $request->validated('note'),
            'sort_order' => $request->validated('sort_order', 0),
            'active' => $request->boolean('active', true),
            'button_text' => $request->validated('button_text'),
            'public_visibility' => $request->boolean('public_visibility', true),
            'renewal_required' => $request->boolean('renewal_required'),
            'renewal_enabled' => $request->boolean('renewal_enabled'),
            'renewal_price' => $request->validated('renewal_price'),
            'renewal_period' => $request->validated('renewal_period'),
            'renewal_after_months' => $request->validated('renewal_after_months', 12),
            'renewal_includes' => $request->validated('renewal_includes', []),
            'renewal_public_text' => $request->validated('renewal_public_text'),
            'show_renewal_price' => $request->boolean('show_renewal_price', true),
        ]);

        $this->syncFeatures($package, $request->validated('features', []));

        return redirect()
            ->route('admin.packages.index')
            ->with('status', 'Paquete creado correctamente.');
    }

    public function edit(Package $package): View
    {
        $package->load('featureItems');

        return view('admin.packages.edit', [
            'package' => $package,
        ]);
    }

    public function update(UpdatePackageRequest $request, Package $package): RedirectResponse
    {
        $package->update([
            'name' => $request->validated('name'),
            'slug' => $request->validated('slug') ?? Str::slug($request->validated('name')),
            'short_description' => $request->validated('short_description'),
            'long_description' => $request->validated('long_description'),
            'price' => $request->validated('price'),
            'currency' => $request->validated('currency', 'MXN'),
            'price_type' => $request->validated('price_type'),
            'direct_checkout' => $request->boolean('direct_checkout'),
            'requires_quote' => $request->boolean('requires_quote'),
            'deposit_percentage' => $request->boolean('direct_checkout') ? 100 : null,
            'featured' => $request->boolean('featured'),
            'is_featured' => $request->boolean('is_featured'),
            'badge' => $request->validated('badge'),
            'note' => $request->validated('note'),
            'sort_order' => $request->validated('sort_order', 0),
            'active' => $request->boolean('active', true),
            'button_text' => $request->validated('button_text'),
            'public_visibility' => $request->boolean('public_visibility', true),
            'renewal_required' => $request->boolean('renewal_required'),
            'renewal_enabled' => $request->boolean('renewal_enabled'),
            'renewal_price' => $request->validated('renewal_price'),
            'renewal_period' => $request->validated('renewal_period'),
            'renewal_after_months' => $request->validated('renewal_after_months', 12),
            'renewal_includes' => $request->validated('renewal_includes', []),
            'renewal_public_text' => $request->validated('renewal_public_text'),
            'show_renewal_price' => $request->boolean('show_renewal_price', true),
        ]);

        $this->syncFeatures($package, $request->validated('features', []));

        return redirect()
            ->route('admin.packages.index')
            ->with('status', 'Paquete actualizado correctamente.');
    }

    public function toggleActive(Package $package): RedirectResponse
    {
        $package->update(['active' => ! $package->active]);

        $status = $package->active ? 'activado' : 'desactivado';

        return redirect()
            ->route('admin.packages.index')
            ->with('status', "Paquete {$status} correctamente.");
    }

    private function syncFeatures(Package $package, array $features): void
    {
        $package->featureItems()->delete();

        foreach ($features as $index => $feature) {
            PackageFeature::create([
                'package_id' => $package->id,
                'title' => $feature['title'],
                'description' => $feature['description'] ?? null,
                'visible_summary' => $feature['visible_summary'] ?? false,
                'sort_order' => $index + 1,
                'active' => true,
            ]);
        }
    }
}
