<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
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

        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        $userRoleValue = is_object($user->role) ? $user->role->value : (string) $user->role;
        $hasAccess = false;
        foreach ($allowedRoles as $allowed) {
            if ($user->isRole($allowed) || $userRoleValue === $allowed || (string) $user->role === $allowed) {
                $hasAccess = true;
                break;
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
