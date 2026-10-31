<?php

namespace App\Http\Middleware;

use App\Models\Assessment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAplApproved
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

        if (! $assessment || $assessment->apl_status !== 'APPROVED') {
            if ($assessment) {
                return redirect()->route('asesi.assessment.apl', $assessment)
                    ->with('error', 'Menunggu persetujuan asesor');
            }

            return redirect()->back()->with('error', 'Menunggu persetujuan asesor');
        }

        return $next($request);
    }
}
