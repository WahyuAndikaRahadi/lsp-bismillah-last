<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentApplication;
use App\Models\AssessmentSchedule;
use App\Models\CertificationScheme;
use App\Models\Event;
use App\Services\BnspExportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BnspExportController extends Controller
{
    public function __construct(
        protected BnspExportService $exportService
    ) {}

    /**
     * Halaman antarmuka preview ekspor 16-kolom BNSP dengan filter
     */
    public function index(Request $request): View
    {
        $schemes = CertificationScheme::where('is_active', true)->get();
        $events = Event::latest()->get();
        $schedules = AssessmentSchedule::latest('tgl_asesmen')->get();

        $query = AssessmentApplication::with([
            'user.asesiProfile',
            'scheme',
            'schedule.tuk',
            'schedule.event',
            'asesor.asesorProfile',
            'ak02',
        ])
            ->whereNotNull('schedule_id');

        if ($request->filled('schedule_id')) {
            $query->where('schedule_id', $request->schedule_id);
        }

        if ($request->filled('scheme_id')) {
            $query->where('scheme_id', $request->scheme_id);
        }

        if ($request->filled('event_id')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }

        $applications = $query->latest()->paginate(20);

        return view('admin.exports.bnsp', compact('applications', 'schemes', 'events', 'schedules'));
    }

    /**
     * Download Excel Spreadsheet 16-kolom sesuai filter
     */
    public function export(Request $request): StreamedResponse
    {
        $query = AssessmentApplication::with([
            'user.asesiProfile',
            'scheme',
            'schedule.tuk',
            'schedule.event',
            'asesor.asesorProfile',
            'ak02',
        ])
            ->whereNotNull('schedule_id');

        if ($request->filled('schedule_id')) {
            $query->where('schedule_id', $request->schedule_id);
        }

        if ($request->filled('scheme_id')) {
            $query->where('scheme_id', $request->scheme_id);
        }

        if ($request->filled('event_id')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }

        $applications = $query->get();
        $filename = 'exporthasilasesmen_'.date('Ymd_His').'.xls';

        return $this->exportService->exportXmlSpreadsheet($applications, $filename);
    }

    /**
     * Download Excel Spreadsheet 16-kolom untuk jadwal tertentu
     */
    public function exportSchedule(AssessmentSchedule $schedule): StreamedResponse
    {
        $applications = AssessmentApplication::with([
            'user.asesiProfile',
            'scheme',
            'schedule.tuk',
            'asesor.asesorProfile',
            'ak02',
        ])
            ->where('schedule_id', $schedule->id)
            ->get();

        $filename = "exporthasilasesmen_{$schedule->kode_jadwal}.xls";

        return $this->exportService->exportXmlSpreadsheet($applications, $filename);
    }
}
