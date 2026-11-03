<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceEvent;
use App\Models\Schedule;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function reverse(
        Schedule $schedule,
        AttendanceEvent $event,
        Request $request,
        AttendanceService $attendanceService
    ): RedirectResponse {
        $user = auth()->user();
        if (! $user->isAdmin()) {
            abort(403, 'Akses terbatas hanya untuk administrator.');
        }

        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $attendanceService->reverseAttendance($event, $user, $request->input('reason'));

        return back()->with('success', 'Catatan kehadiran berhasil dibatalkan secara tertelusur.');
    }
}
