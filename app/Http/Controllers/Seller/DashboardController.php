<?php

declare(strict_types=1);

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->loadMissing('sellerProfile');
        $sellerId = $user->id;

        $ordersQuery = Order::query()->where('seller_id', $sellerId);
        $commissionQuery = Commission::query()->where('seller_id', $sellerId);

        $referralUrl = $user->sellerProfile
            ? route('home', ['ref' => $user->sellerProfile->referral_code])
            : null;

        return view('seller.dashboard', [
            'user' => $user,
            'referralUrl' => $referralUrl,
            'referralCode' => $user->sellerProfile?->referral_code,
            'metrics' => [
                'sales' => (clone $ordersQuery)->count(),
                'customers' => (clone $ordersQuery)->whereNotNull('customer_id')->distinct('customer_id')->count('customer_id'),
                'commissions_generated' => (float) (clone $commissionQuery)->sum('commission_amount'),
                'commissions_pending' => (float) (clone $commissionQuery)->whereIn('status', ['pending', 'available'])->sum('commission_amount'),
            ],
        ]);
    }
}
