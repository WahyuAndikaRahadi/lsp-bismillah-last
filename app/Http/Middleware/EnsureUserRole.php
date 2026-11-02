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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
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
            $dashboardRoute = $user->dashboardRoute();
            $message = 'Anda dialihkan ke dashboard Anda karena tidak memiliki akses ke halaman tersebut.';

            return redirect()->route($dashboardRoute)
                ->with('warning', $message)
                ->with('error', $message);
        }

        return $next($request);
    }
}
