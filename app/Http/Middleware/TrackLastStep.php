<?php

namespace App\Http\Middleware;

use App\Models\Assessment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLastStep
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $assessment = $request->route('assessment');

        if (is_numeric($assessment) || is_string($assessment)) {
            $assessment = Assessment::find($assessment);
        }

        if ($assessment instanceof Assessment) {
            $routeName = $request->route()?->getName();

            if ($routeName) {
                $segments = explode('.', $routeName);
                $step = end($segments);

                if ($step) {
                    $assessment->update(['last_active_step' => $step]);
                }
            }
        }

        return $next($request);
    }
}
