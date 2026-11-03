<?php

namespace App\Services;

use App\Models\LspProfile;
use App\Models\Scheme;
use App\Models\SchemeVersion;
use DomainException;
use Illuminate\Support\Facades\DB;

class SchemeReferenceService
{
    /**
     * Validate structure, KUK integrity, and question answer keys of reference data.
     *
     * @param array $data
     * @return array List of pinpointed validation error messages
     */
    public function validate(array $data): array
    {
        $errors = [];

        // Required top-level keys
        foreach (['code', 'name', 'scheme_type'] as $reqField) {
            if (empty($data[$reqField])) {
                $errors[] = "Field skema '{$reqField}' wajib diisi.";
            }
        }

        $units = $data['units'] ?? [];
        if (empty($units) || ! is_array($units)) {
            $errors[] = "Daftar 'units' wajib disertakan dan tidak boleh kosong.";

            return $errors;
        }

        $seenKukCodes = [];

        foreach ($units as $uIdx => $unit) {
            $unitCode = $unit['code'] ?? "unit-index-{$uIdx}";
            if (empty($unit['code'])) {
                $errors[] = "Field 'code' pada unit index {$uIdx} wajib diisi.";
            }
            if (empty($unit['title'])) {
                $errors[] = "Field 'title' pada unit {$unitCode} wajib diisi.";
            }

            $elements = $unit['elements'] ?? [];
            foreach ($elements as $eIdx => $elem) {
                $elemName = $elem['name'] ?? $elem['title'] ?? "elemen-{$eIdx}";
                $kuks = $elem['kuks'] ?? [];

                foreach ($kuks as $kIdx => $kuk) {
                    $kukCode = $kuk['code'] ?? null;
                    if (empty($kukCode)) {
                        $errors[] = "KUK index {$kIdx} pada elemen '{$elemName}' (unit: {$unitCode}) tidak memiliki 'code'.";
                        continue;
                    }

                    // Check for duplicate KUK within unit
                    $scopedKey = "{$unitCode}:{$kukCode}";
                    if (isset($seenKukCodes[$scopedKey])) {
                        $errors[] = "Duplikasi KUK code '{$kukCode}' ditemukan pada unit '{$unitCode}'.";
                    }
                    $seenKukCodes[$scopedKey] = true;

                    if (empty($kuk['criteria']) && empty($kuk['statement'])) {
                        $errors[] = "KUK '{$kukCode}' pada unit '{$unitCode}' tidak memiliki kriteria/statement.";
                    }
                }
            }

            // Validate CBT / IA-05 questions if present
            $questions = $unit['questions'] ?? [];
            foreach ($questions as $qIdx => $question) {
                $num = $qIdx + 1;
                if (! isset($question['answer_key']) || trim((string) $question['answer_key']) === '') {
                    $errors[] = "Kunci jawaban (answer_key) tidak boleh kosong pada pertanyaan nomor {$num} (unit: {$unitCode}).";
                }
                if (empty($question['options']) || ! is_array($question['options'])) {
                    $errors[] = "Pilihan jawaban (options) harus berupa array pada pertanyaan nomor {$num} (unit: {$unitCode}).";
                }
            }
        }

        return $errors;
    }

    /**
     * Import reference data in dry-run or apply mode.
     */
    public function import(array $data, bool $dryRun = true): array
    {
        $errors = $this->validate($data);
        if (! empty($errors)) {
            return [
                'success' => false,
                'dry_run' => $dryRun,
                'errors' => $errors,
            ];
        }

        $sourceHash = hash('sha256', json_encode($data));

        if ($dryRun) {
            return [
                'success' => true,
                'dry_run' => true,
                'scheme_code' => $data['code'],
                'scheme_name' => $data['name'],
                'units_count' => count($data['units'] ?? []),
                'source_hash' => $sourceHash,
                'action' => 'validated_read_only',
            ];
        }

        return DB::transaction(function () use ($data, $sourceHash) {
            $scheme = Scheme::firstOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'scheme_type' => $data['scheme_type'] ?? 'KLASTER',
                ]
            );

            // Check if identical source hash version exists
            $existingVersion = SchemeVersion::where('scheme_id', $scheme->id)
                ->where('source_hash', $sourceHash)
                ->first();

            if ($existingVersion) {
                if (in_array($existingVersion->status, ['approved', 'published'], true)) {
                    // Idempotent: Never overwrite approved/published reference
                    return [
                        'success' => true,
                        'dry_run' => false,
                        'action' => 'skipped_approved',
                        'scheme_id' => $scheme->id,
                        'version' => $existingVersion->version,
                        'status' => $existingVersion->status,
                        'source_hash' => $sourceHash,
                    ];
                }

                // Update existing draft
                $existingVersion->update([
                    'title' => $data['name'],
                    'notes' => 'Draft re-imported from source reference',
                ]);

                return [
                    'success' => true,
                    'dry_run' => false,
                    'action' => 'updated_draft',
                    'scheme_id' => $scheme->id,
                    'version' => $existingVersion->version,
                    'status' => 'draft',
                    'source_hash' => $sourceHash,
                ];
            }

            // New version: increment version string
            $currentCount = SchemeVersion::where('scheme_id', $scheme->id)->count();
            $nextVersionStr = 'v'.($currentCount + 1).'.0';

            // New version always starts as draft; never auto-approved
            $newVersion = SchemeVersion::create([
                'scheme_id' => $scheme->id,
                'version' => $nextVersionStr,
                'code' => $data['code'],
                'title' => $data['name'],
                'status' => 'draft',
                'source_hash' => $sourceHash,
                'notes' => 'Draft version awaiting formal approval and validation evidence.',
            ]);

            return [
                'success' => true,
                'dry_run' => false,
                'action' => 'created_draft',
                'scheme_id' => $scheme->id,
                'version' => $newVersion->version,
                'status' => 'draft',
                'source_hash' => $sourceHash,
            ];
        });
    }

    /**
     * Publish an approved scheme version with formal reviewer evidence and LSP configuration check.
     */
    public function publishVersion(int $versionId, array $approvalEvidence): SchemeVersion
    {
        // 1. LSP Profile Configuration Check
        $lspProfile = LspProfile::current();
        if (! $lspProfile || empty($lspProfile->nama_lsp) || empty($lspProfile->no_lisensi_bnsp) || empty($lspProfile->nama_ketua)) {
            throw new DomainException('Konfigurasi resmi LSP (nama, no lisensi, ketua) belum lengkap. Penerbitan skema ditolak.');
        }

        // 2. Reviewer / Trial Evidence Check (SOP 04 §2.5 / SOP 05)
        if (empty($approvalEvidence['reviewer_name']) || empty($approvalEvidence['review_date'])) {
            throw new DomainException('Bukti review validator (reviewer_name, review_date) wajib disertakan untuk persetujuan skema.');
        }

        $version = SchemeVersion::findOrFail($versionId);

        if ($version->status === 'published') {
            return $version;
        }

        return DB::transaction(function () use ($version, $approvalEvidence) {
            // Retire previous published versions of this scheme
            SchemeVersion::where('scheme_id', $version->scheme_id)
                ->where('status', 'published')
                ->update(['status' => 'retired']);

            $evidenceJson = json_encode($approvalEvidence);
            $version->update([
                'status' => 'published',
                'notes' => "Published with evidence: {$evidenceJson}",
            ]);

            return $version->fresh();
        });
    }

    /**
     * Retire a scheme version without deleting or corrupting historical assessment snapshots.
     */
    public function retireVersion(int $versionId, string $reason): SchemeVersion
    {
        $version = SchemeVersion::findOrFail($versionId);

        $version->update([
            'status' => 'retired',
            'notes' => ($version->notes ? $version->notes." | " : '')."Retired: {$reason}",
        ]);

        return $version->fresh();
    }
}
