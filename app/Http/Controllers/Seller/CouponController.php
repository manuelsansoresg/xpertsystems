<?php

declare(strict_types=1);

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

final class CouponController extends Controller
{
    public function index(Request $request)
    {
        $seller = $request->user();

        $coupons = Coupon::query()
            ->with('packages')
            ->where('seller_id', $seller->id)
            ->latest()
            ->paginate(25);

        return view('seller.coupons.index', compact('coupons'));
    }
}
