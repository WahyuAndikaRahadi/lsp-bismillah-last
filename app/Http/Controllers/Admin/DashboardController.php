<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Scheme;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_schedules' => Schedule::count(),
            'total_asesi' => User::where('role', 'ASESI')->count(),
            'total_asesor' => User::where('role', 'ASESOR')->count(),
            'total_schemes' => Scheme::active()->count(),
        ];

        $recentSchedules = Schedule::with(['scheme'])
            ->latest('date')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentSchedules'
        ));
    }
}
