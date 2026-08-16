<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->with('roles');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('slug', $role));
        }

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'active' => $request->get('active'),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $user = User::create([
                'name' => $request->validated('name'),
                'first_name' => $request->validated('first_name') ?? str($request->validated('name'))->before(' ')->toString(),
                'last_name' => $request->validated('last_name') ?? str($request->validated('name'))->after(' ')->when(fn ($s) => $s->isNotEmpty())->toString() ?: null,
                'email' => str($request->validated('email'))->lower()->toString(),
                'phone' => $request->validated('phone'),
                'password' => $request->validated('password'),
                'active' => $request->boolean('active', true),
                'email_verified_at' => now(),
            ]);

            $role = Role::query()->where('slug', $request->validated('role'))->firstOrFail();
            $user->roles()->attach($role, ['assigned_by' => $request->user()->id]);

            if ($request->validated('role') === 'seller') {
                SellerProfile::create([
                    'user_id' => $user->id,
                    'referral_code' => $request->validated('referral_code'),
                    'commission_type' => $request->validated('commission_type'),
                    'commission_value' => $request->validated('commission_value'),
                    'payment_method' => $request->validated('payment_method'),
                    'payment_details' => $request->validated('payment_details'),
                    'notes' => $request->validated('notes'),
                ]);
            }
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        $user->load(['roles', 'sellerProfile']);

        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        DB::transaction(function () use ($request, $user): void {
            $data = $request->only(['name', 'first_name', 'last_name', 'phone', 'active']);
            $data['email'] = str($request->validated('email'))->lower()->toString();

            if ($request->filled('password')) {
                $data['password'] = $request->validated('password');
            }

            $data['first_name'] = $data['first_name'] ?? str($request->validated('name'))->before(' ')->toString();
            $data['last_name'] = $data['last_name'] ?? str($request->validated('name'))->after(' ')->when(fn ($s) => $s->isNotEmpty())->toString() ?: null;

            $user->update($data);

            $role = Role::query()->where('slug', $request->validated('role'))->firstOrFail();
            $user->roles()->sync([$role->id => ['assigned_by' => $request->user()->id]]);

            if ($request->validated('role') === 'seller') {
                $profileData = [
                    'referral_code' => $request->validated('referral_code'),
                    'commission_type' => $request->validated('commission_type'),
                    'commission_value' => $request->validated('commission_value'),
                    'payment_method' => $request->validated('payment_method'),
                    'payment_details' => $request->validated('payment_details'),
                    'notes' => $request->validated('notes'),
                ];

                if ($user->sellerProfile) {
                    $user->sellerProfile->update($profileData);
                } else {
                    SellerProfile::create(array_merge($profileData, ['user_id' => $user->id]));
                }
            }
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'No puedes desactivar tu propio usuario.');
        }

        $user->update(['active' => ! $user->active]);

        $status = $user->active ? 'activado' : 'desactivado';

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Usuario {$status} correctamente.");
    }
}
