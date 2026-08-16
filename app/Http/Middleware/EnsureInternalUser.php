<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureInternalUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user()?->loadMissing('roles');

        if (! $user || ! $user->active || ! $user->hasAnyRole(['admin', 'seller'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->with('auth_error', 'Tu acceso interno no está disponible.');
        }

        return $next($request);
    }
}
