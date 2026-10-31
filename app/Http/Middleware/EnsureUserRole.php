<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi Administrator LSP.');
        }

        $userRoleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        $hasAccess = false;
        foreach ($roles as $roleGroup) {
            foreach (explode(',', $roleGroup) as $r) {
                $r = trim($r);
                if ($user->isRole($r) || $userRoleValue === $r) {
                    $hasAccess = true;
                    break 2;
                }
            }
        }

        if (! $hasAccess) {
            // Redirect user to their own proper dashboard instead of generic 403 where applicable
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
            }
            if ($user->isAsesor()) {
                return redirect()->route('asesor.dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
            }
            if ($user->isAsesi()) {
                return redirect()->route('asesi.dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
            }

            abort(403, 'Akses tidak diizinkan untuk peran akun Anda.');
        }

        return $next($request);
    }
}
