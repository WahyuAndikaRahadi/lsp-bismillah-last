<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $asesi = $request->user();
        $latestApplication = $asesi->applications()
            ->with(['scheme', 'schedule.tuk', 'documents'])
            ->latest()
            ->first();

        $applicationsCount = $asesi->applications()->count();

        return view('asesi.dashboard', [
            'asesi' => $asesi,
            'latestApplication' => $latestApplication,
            'applicationsCount' => $applicationsCount,
            'profile' => $asesi->asesiProfile,
        ]);
    }
}
