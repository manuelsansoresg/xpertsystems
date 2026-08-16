<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Renewal;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->loadMissing(['roles', 'sellerProfile']);

        if ($user->isSeller() && ! $user->isAdmin()) {
            return $this->sellerDashboard($user);
        }

        $startOfMonth = now()->startOfMonth();
        $startOfYear = now()->startOfYear();
        $received = (float) Payment::query()->whereNotNull('paid_at')->sum('amount');
        $sold = (float) Order::query()->whereNot('status', 'cancelled')->sum('total_amount');

        $metrics = [
            ['label' => 'Ventas del mes', 'value' => Order::query()->where('created_at', '>=', $startOfMonth)->count(), 'kind' => 'number', 'note' => 'Órdenes registradas'],
            ['label' => 'Ventas del año', 'value' => Order::query()->where('created_at', '>=', $startOfYear)->count(), 'kind' => 'number', 'note' => 'Acumulado anual'],
            ['label' => 'Monto vendido', 'value' => $sold, 'kind' => 'money', 'note' => 'Valor comercial total'],
            ['label' => 'Pagos recibidos', 'value' => $received, 'kind' => 'money', 'note' => 'Movimientos confirmados'],
            ['label' => 'Pendiente por cobrar', 'value' => max(0, $sold - $received), 'kind' => 'money', 'note' => 'Saldo estimado'],
            ['label' => 'Vendedores activos', 'value' => User::query()->where('active', true)->whereHas('roles', fn ($query) => $query->where('slug', 'seller'))->count(), 'kind' => 'number', 'note' => 'Equipo comercial'],
        ];

        $months = collect(range(5, 0))->map(function (int $offset): array {
            $month = CarbonImmutable::now()->subMonths($offset);
            $query = Order::query()->whereBetween('created_at', [$month->startOfMonth(), $month->endOfMonth()]);

            return [
                'label' => $month->locale('es')->translatedFormat('M'),
                'sales' => (int) (clone $query)->count(),
                'revenue' => (float) $query->sum('total_amount'),
            ];
        });

        return view('admin.dashboard', [
            'mode' => 'admin',
            'metrics' => $metrics,
            'months' => $months,
            'chartMax' => max(1, (float) $months->max('revenue')),
            'recentOrders' => Order::query()->with(['package', 'seller'])->latest()->limit(6)->get(),
            'commercialSummary' => [
                'customers' => Customer::query()->count(),
                'attributedSales' => Order::query()->whereNotNull('seller_id')->count(),
                'pendingCommissions' => (float) Commission::query()->whereIn('status', ['pending', 'available'])->sum('commission_amount'),
                'upcomingRenewals' => Renewal::query()->whereBetween('renewal_date', [today(), today()->addDays(30)])->count(),
                'overdueRenewals' => Renewal::query()->where('renewal_date', '<', today())->where('status', 'overdue')->count(),
            ],
        ]);
    }

    private function sellerDashboard(User $seller): View
    {
        $orders = Order::query()->where('seller_id', $seller->id);
        $commission = Commission::query()->where('seller_id', $seller->id);

        return view('admin.dashboard', [
            'mode' => 'seller',
            'metrics' => [
                ['label' => 'Ventas este mes', 'value' => (clone $orders)->where('created_at', '>=', now()->startOfMonth())->count(), 'kind' => 'number', 'note' => 'Atribuidas a ti'],
                ['label' => 'Ventas totales', 'value' => (clone $orders)->count(), 'kind' => 'number', 'note' => 'Histórico'],
                ['label' => 'Clientes obtenidos', 'value' => (clone $orders)->whereNotNull('customer_id')->distinct('customer_id')->count('customer_id'), 'kind' => 'number', 'note' => 'Clientes únicos'],
                ['label' => 'Comisión generada', 'value' => (float) (clone $commission)->sum('commission_amount'), 'kind' => 'money', 'note' => 'Histórico'],
                ['label' => 'Saldo pendiente', 'value' => (float) (clone $commission)->whereIn('status', ['pending', 'available'])->sum('commission_amount'), 'kind' => 'money', 'note' => 'Por liquidar'],
            ],
            'months' => collect(),
            'chartMax' => 1,
            'recentOrders' => (clone $orders)->with('package')->latest()->limit(6)->get(),
            'commercialSummary' => [],
            'referralUrl' => $seller->sellerProfile
                ? route('home', ['ref' => $seller->sellerProfile->referral_code])
                : null,
        ]);
    }
}
