<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query()->with(['seller', 'packages']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            match ($status) {
                'active' => $query->where('is_active', true)->whereNull('expires_at')->orWhere('expires_at', '>', now()),
                'inactive' => $query->where('is_active', false),
                'expired' => $query->where('expires_at', '<', now()),
                'exhausted' => $query->whereColumn('times_used', '>=', 'usage_limit')->whereNotNull('usage_limit'),
                default => null,
            };
        }

        if ($type = $request->input('type')) {
            $query->where('discount_type', $type);
        }

        if ($scope = $request->input('scope')) {
            $query->where('scope', $scope);
        }

        if ($sellerId = $request->input('seller_id')) {
            if ($sellerId === 'general') {
                $query->whereNull('seller_id');
            } else {
                $query->where('seller_id', $sellerId);
            }
        }

        $coupons = $query->latest()->paginate(25)->withQueryString();
        $sellers = User::query()->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))->orderBy('name')->get();

        return view('admin.coupons.index', compact('coupons', 'sellers'));
    }

    public function create()
    {
        $packages = Package::query()->where('is_active', true)->orderBy('name')->get();
        $sellers = User::query()->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))->orderBy('name')->get();

        return view('admin.coupons.create', compact('packages', 'sellers'));
    }

    public function store(StoreCouponRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $packageIds = $data['package_ids'] ?? [];
        unset($data['package_ids']);

        DB::transaction(function () use ($data, $packageIds, &$coupon) {
            $coupon = Coupon::create($data);

            if ($coupon->scope === CouponScope::Packages && !empty($packageIds)) {
                $coupon->packages()->sync($packageIds);
            }
        });

        return redirect()
            ->route('admin.coupons.show', $coupon)
            ->with('success', 'Cupón creado correctamente.');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['seller', 'packages', 'redemptions.customer', 'creator']);

        $metrics = [
            'total_uses' => $coupon->times_used,
            'total_discount' => $coupon->redemptions()->sum('discount_amount'),
            'total_revenue' => 0,
        ];

        return view('admin.coupons.show', compact('coupon', 'metrics'));
    }

    public function edit(Coupon $coupon)
    {
        $packages = Package::query()->orderBy('name')->get();
        $sellers = User::query()->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))->orderBy('name')->get();
        $selectedPackageIds = $coupon->packages()->pluck('packages.id')->toArray();

        return view('admin.coupons.edit', compact('coupon', 'packages', 'sellers', 'selectedPackageIds'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $data = $request->validated();
        $packageIds = $data['package_ids'] ?? [];
        unset($data['package_ids']);

        DB::transaction(function () use ($data, $packageIds, $coupon) {
            $coupon->update($data);

            if ($coupon->scope === CouponScope::Packages) {
                $coupon->packages()->sync($packageIds);
            } else {
                $coupon->packages()->detach();
            }
        });

        return redirect()
            ->route('admin.coupons.show', $coupon)
            ->with('success', 'Cupón actualizado correctamente.');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $status = $coupon->is_active ? 'activado' : 'desactivado';

        return redirect()
            ->route('admin.coupons.show', $coupon)
            ->with('success', "Cupón {$status} correctamente.");
    }

    public function duplicate(Coupon $coupon)
    {
        $newCoupon = null;

        DB::transaction(function () use ($coupon, &$newCoupon) {
            $newData = $coupon->toArray();
            unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at']);

            $newCode = $this->generateUniqueCode($coupon->code);
            $newData['code'] = $newCode;
            $newData['times_used'] = 0;
            $newData['name'] = $coupon->name . ' (copia)';

            $newCoupon = Coupon::create($newData);

            if ($coupon->scope === CouponScope::Packages) {
                $packageIds = $coupon->packages()->pluck('packages.id')->toArray();
                $newCoupon->packages()->sync($packageIds);
            }
        });

        return redirect()
            ->route('admin.coupons.edit', $newCoupon)
            ->with('success', 'Cupón duplicado. Edita el nuevo cupón.');
    }

    private function generateUniqueCode(string $baseCode): string
    {
        $suffix = strtoupper(substr(md5(uniqid()), 0, 4));

        return substr($baseCode, 0, 10) . '-' . $suffix;
    }
}
