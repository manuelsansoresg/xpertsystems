<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payout::query()->with(['seller', 'recordedBy']);

        if ($sellerId = $request->get('seller_id')) {
            $query->where('seller_id', $sellerId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $payouts = $query->latest()->paginate(20)->withQueryString();

        $sellers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $summary = [
            'total' => Payout::query()->sum('amount'),
            'pending' => Payout::query()->where('status', 'pending')->sum('amount'),
            'completed' => Payout::query()->where('status', 'completed')->sum('amount'),
        ];

        return view('admin.payouts.index', [
            'payouts' => $payouts,
            'sellers' => $sellers,
            'summary' => $summary,
            'filters' => [
                'seller_id' => $sellerId,
                'status' => $status,
            ],
        ]);
    }

    public function show(Payout $payout): View
    {
        $payout->load(['seller', 'recordedBy', 'commissions']);

        return view('admin.payouts.show', [
            'payout' => $payout,
        ]);
    }
}
