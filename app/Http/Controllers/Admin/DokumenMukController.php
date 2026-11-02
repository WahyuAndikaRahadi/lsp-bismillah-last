<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Schedule;
use App\Services\MukExportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DokumenMukController extends Controller
{
    public function __construct(
        private MukExportService $exporter,
    ) {}

    public function index(): View
    {
        $schedules = Schedule::with('scheme', 'assessments')
            ->latest('date')
            ->paginate(15);

        return view('admin.dokumen-muk.index', compact('schedules'));
    }

    public function exportSchedule(Schedule $schedule, string $type)
    {
        $normalizedType = strtolower(str_replace(['_', ' '], '-', $type));
        $aliasMap = [
            'mapa01' => 'mapa',
            'mapa1' => 'mapa',
            'fr-mapa-01' => 'mapa',
            'fr-mapa-02' => 'mapa02',
            'ia07' => 'ia07',
            'fr-ia-07' => 'ia07',
            'ia11' => 'ia11',
            'fr-ia-11' => 'ia11',
            'daftar-periksa' => 'daftar-periksa',
        ];
        $targetType = $aliasMap[$normalizedType] ?? $normalizedType;

        $data = $this->exporter->getExportData($schedule);
        $data['exportType'] = $targetType;

        $viewName = "admin.dokumen-muk.export-{$targetType}";
        if (view()->exists($viewName)) {
            return view($viewName, $data);
        }

        $asesiViewName = "admin.dokumen-muk.export-asesi-{$targetType}";
        if (view()->exists($asesiViewName)) {
            $firstAssessment = $schedule->assessments->first();
            if ($firstAssessment) {
                $asesiData = $this->exporter->getAsesiExportData($firstAssessment);
                $asesiData['exportType'] = $targetType;

                return view($asesiViewName, $asesiData);
            }
        }

        return view('admin.dokumen-muk.preview-schedule', $data);
    }

    public function exportAsesi(Assessment $assessment, string $type = 'all')
    {
        $normalizedType = strtolower(str_replace(['_', ' '], '-', $type));
        $aliasMap = [
            'mapa' => 'mapa01',
            'mapa1' => 'mapa01',
            'fr-mapa-01' => 'mapa01',
            'fr-mapa-02' => 'mapa02',
            'ia05' => 'ia05a',
            'fr-ia-01' => 'ia01',
            'fr-ia-01b' => 'ia01b',
            'fr-ia-02' => 'ia02',
            'fr-ia-03' => 'ia03',
            'fr-ia-05a' => 'ia05a',
            'fr-ia-05b' => 'ia05b',
            'fr-ia-05c' => 'ia05c',
            'fr-ia-07' => 'ia07',
            'fr-ia-11' => 'ia11',
            'fr-ak-01' => 'ak01',
            'fr-ak-02' => 'ak02',
            'fr-ak-03' => 'ak03',
            'fr-ak-04' => 'ak04',
            'fr-ak-05' => 'ak05',
            'fr-ak-06' => 'ak06',
            'fr-ak-07' => 'ak07',
        ];
        $targetType = $aliasMap[$normalizedType] ?? $normalizedType;

        $data = $this->exporter->getAsesiExportData($assessment);
        $data['exportType'] = $targetType;

        $viewName = "admin.dokumen-muk.export-asesi-{$targetType}";
        if (view()->exists($viewName)) {
            return view($viewName, $data);
        }

        $scheduleView = "admin.dokumen-muk.export-{$targetType}";
        if (view()->exists($scheduleView)) {
            $data['assessors'] = $assessment->schedule?->assessors ?? collect();
            $data['leadAssessor'] = $data['assessor'] ?? $data['assessors']->first();
            $data['assessments'] = $assessment->schedule?->assessments ?? collect([$assessment]);

            return view($scheduleView, $data);
        }

        return view('admin.dokumen-muk.preview-asesi', $data);
    }

    public function exportSertifikat(Schedule $schedule)
    {
        return $this->exportSchedule($schedule, 'sertifikat');
    }

    public function exportBa(Schedule $schedule)
    {
        return $this->exportSchedule($schedule, 'ba');
    }

    public function exportHadirAsesor(Schedule $schedule)
    {
        return $this->exportSchedule($schedule, 'hadir-asesor');
    }

    public function exportHadirAsesi(Schedule $schedule)
    {
        return $this->exportSchedule($schedule, 'hadir-asesi');
    }

    public function exportExcelBiodata(Schedule $schedule, Request $request)
    {
        if ($request->query('format') === 'html') {
            return $this->exportSchedule($schedule, 'excel-biodata');
        }

        return $this->exporter->exportExcelBiodata($schedule);
    }

    public function exportExcelHadirAsesi(Schedule $schedule, Request $request)
    {
        if ($request->query('format') === 'html') {
            return $this->exportHadirAsesi($schedule);
        }

        return $this->exporter->exportExcelDaftarHadirAsesi($schedule);
    }

    public function exportExcelHadirAsesor(Schedule $schedule, Request $request)
    {
        if ($request->query('format') === 'html') {
            return $this->exportHadirAsesor($schedule);
        }

        return $this->exporter->exportExcelDaftarHadirAsesor($schedule);
    }

    public function exportIndividual(Assessment $assessment, Request $request)
    {
        return $this->exportAsesi($assessment, $request->query('type', 'all'));
    }
}
