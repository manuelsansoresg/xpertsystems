<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSellerRequest;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Role;
use App\Models\SellerProfile;
use App\Models\User;
use App\Services\ReferralCodeGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class SellerController extends Controller
{
    public function __construct(
        private readonly ReferralCodeGenerator $codeGenerator = new ReferralCodeGenerator(),
    ) {}

    public function index(Request $request): View
    {
        $query = User::query()
            ->with(['roles', 'sellerProfile'])
            ->whereHas('roles', fn ($q) => $q->where('slug', 'seller'));

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('sellerProfile', fn ($sp) => $sp->where('referral_code', 'like', "%{$search}%"));
            });
        }

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($commissionType = $request->get('commission_type')) {
            $query->whereHas('sellerProfile', fn ($sp) => $sp->where('commission_type', $commissionType));
        }

        $sellers = $query->latest()->paginate(15)->withQueryString();

        $sellers->each(function (User $seller): void {
            $seller->setAttribute('orders_count', Order::query()->where('seller_id', $seller->id)->count());
            $seller->setAttribute('customers_count', \App\Models\Customer::query()->where('seller_id', $seller->id)->count());
            $seller->setAttribute('sales_sum', (float) Order::query()->where('seller_id', $seller->id)->sum('total_amount'));
            $seller->setAttribute('commissions_sum', (float) Commission::query()->where('seller_id', $seller->id)->sum('commission_amount'));
        });

        return view('admin.sellers.index', [
            'sellers' => $sellers,
            'filters' => [
                'search' => $search,
                'active' => $request->get('active'),
                'commission_type' => $commissionType,
            ],
        ]);
    }



    public function show(SellerProfile $seller): View
    {
        $seller->load('user.roles');

        $sellerId = $seller->user_id;

        $coupons = \App\Models\Coupon::query()
            ->with('packages')
            ->where('seller_id', $sellerId)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.sellers.show', [
            'seller' => $seller,
            'referralUrl' => route('home', ['ref' => $seller->referral_code]),
            'metrics' => [
                'customers' => Order::query()->where('seller_id', $sellerId)->whereNotNull('customer_id')->distinct('customer_id')->count('customer_id'),
                'sales' => Order::query()->where('seller_id', $sellerId)->count(),
                'sales_sum' => (float) Order::query()->where('seller_id', $sellerId)->sum('total_amount'),
                'commissions_sum' => (float) Commission::query()->where('seller_id', $sellerId)->sum('commission_amount'),
            ],
            'coupons' => $coupons,
        ]);
    }

    public function edit(SellerProfile $seller): View
    {
        $seller->load('user.roles');

        return view('admin.sellers.edit', ['seller' => $seller]);
    }

    public function update(UpdateSellerRequest $request, SellerProfile $seller): RedirectResponse
    {
        DB::transaction(function () use ($request, $seller): void {
            $user = $seller->user;

            $data = $request->only(['name', 'last_name', 'phone', 'active']);
            $data['email'] = str($request->validated('email'))->lower()->toString();

            if ($request->filled('password')) {
                $data['password'] = $request->validated('password');
            }

            $user->update($data);

            $seller->update([
                'referral_code' => strtoupper($request->validated('referral_code')),
                'commission_type' => $request->validated('commission_type'),
                'commission_value' => $request->validated('commission_value'),
                'payment_method' => $request->validated('payment_method'),
                'payment_details' => $request->validated('payment_details'),
                'notes' => $request->validated('notes'),
            ]);
        });

        return redirect()
            ->route('admin.sellers.index')
            ->with('status', 'Vendedor actualizado correctamente.');
    }

    public function toggleActive(Request $request, SellerProfile $seller): RedirectResponse
    {
        $user = $seller->user;

        if ($user->is($request->user())) {
            return redirect()
                ->route('admin.sellers.index')
                ->with('error', 'No puedes desactivar tu propio usuario.');
        }

        $user->update(['active' => ! $user->active]);

        $status = $user->active ? 'activado' : 'desactivado';

        return redirect()
            ->route('admin.sellers.index')
            ->with('status', "Vendedor {$status} correctamente.");
    }

    public function generateCode(Request $request): JsonResponse
    {
        $name = $request->get('name', '');
        $code = $this->codeGenerator->generate($name);

        return response()->json(['code' => $code]);
    }
}
