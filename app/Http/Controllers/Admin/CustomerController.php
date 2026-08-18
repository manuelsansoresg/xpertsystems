<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query()->with('seller');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($source = $request->get('source')) {
            $query->where('source', $source);
        }

        if ($request->has('seller_id')) {
            $sellerId = $request->get('seller_id');
            if ($sellerId === '') {
                $query->whereNull('seller_id');
            } else {
                $query->where('seller_id', $sellerId);
            }
        }

        if ($country = $request->get('country')) {
            $query->where('country', $country);
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        $sellers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $countries = Customer::query()
            ->whereNotNull('country')
            ->distinct()
            ->pluck('country')
            ->sort()
            ->values();

        return view('admin.customers.index', [
            'customers' => $customers,
            'sellers' => $sellers,
            'countries' => $countries,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'source' => $source,
                'seller_id' => $request->get('seller_id'),
                'country' => $country,
            ],
        ]);
    }

    public function create(): View
    {
        $sellers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.customers.create', [
            'sellers' => $sellers,
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::create($request->validated());

        return redirect()
            ->route('admin.customers.index')
            ->with('status', 'Cliente creado correctamente.');
    }

    public function show(Customer $customer): View
    {
        $customer->load('seller');

        $salesCount = $customer->orders()->where('status', 'completed')->count();
        $salesSum = (float) $customer->orders()->where('status', 'completed')->sum('total_amount');

        return view('admin.customers.show', [
            'customer' => $customer,
            'salesCount' => $salesCount,
            'salesSum' => $salesSum,
        ]);
    }

    public function edit(Customer $customer): View
    {
        $customer->load('seller');

        $sellers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'seller'))
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.customers.edit', [
            'customer' => $customer,
            'sellers' => $sellers,
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()
            ->route('admin.customers.index')
            ->with('status', 'Cliente actualizado correctamente.');
    }

    public function toggleStatus(Customer $customer): RedirectResponse
    {
        $newStatus = $customer->status === 'inactive' ? 'customer' : 'inactive';
        $customer->update(['status' => $newStatus]);

        $status = $newStatus === 'inactive' ? 'inactivado' : 'reactivado';

        return redirect()
            ->route('admin.customers.index')
            ->with('status', "Cliente {$status} correctamente.");
    }
}
