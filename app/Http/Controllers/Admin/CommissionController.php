<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Commission::query()->with(['seller', 'order']);

        if ($sellerId = $request->get('seller_id')) {
            $query->where('seller_id', $sellerId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $commissions = $query->latest()->paginate(20)->withQueryString();

        $sellers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $summary = [
            'total' => Commission::query()->sum('commission_amount'),
            'pending' => Commission::query()->where('status', 'pending')->sum('commission_amount'),
            'available' => Commission::query()->where('status', 'available')->sum('commission_amount'),
            'paid' => Commission::query()->where('status', 'paid')->sum('commission_amount'),
        ];

        return view('admin.commissions.index', [
            'commissions' => $commissions,
            'sellers' => $sellers,
            'summary' => $summary,
            'filters' => [
                'seller_id' => $sellerId,
                'status' => $status,
            ],
        ]);
    }

    public function show(Commission $commission): View
    {
        $commission->load(['seller', 'order', 'payouts']);

        return view('admin.commissions.show', [
            'commission' => $commission,
        ]);
    }
}
