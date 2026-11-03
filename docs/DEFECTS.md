# Defect baseline wajib diperbaiki

Ini temuan audit sebelumnya untuk dibuktikan oleh implementasi baru, bukan klaim test baru telah lulus. Path controller pendek relatif `app/Http/Controllers/`.

| ID / severity | Lokasi baseline | Perbaikan + acceptance negatif | Task |
|---|---|---|---|
| D01 kritis: collision ID dua domain | `PdfDocumentController.php`, `Admin/DokumenMukController.php`, `app/Services/MukExportService.php` | Hapus fallback `Assessment::find(id) ?? AssessmentApplication::find...`; explicit migration map; fixture ID sama tidak membocorkan objek lain | 01,03,22 |
| D02 kritis: file/document tanpa object guard | `SecureFileController.php`, `PdfDocumentController.php`, `routes/web.php`, `config/filesystems.php` | Policy sebelum lookup file, private storage; tamu/user lain/asesor lain/path traversal ditolak | 01,04 |
| D03 kritis: GET mengubah consent/hasil | `Asesi/RuangUjiController.php`, `Asesor/WorkspaceController.php`, `app/Services/AkWorkflowService.php` | GET/HEAD dua kali tidak mengubah domain; explicit POST+CSRF+idempotency | 01,11,16 |
| D04 kritis: kompetensi otomatis | workspace, `Asesor/InstrumentController.php`, `Asesor/FinalizationController.php`, `AkWorkflowService.php` | No default K atau CBT→K; unset findings memblokir finalisasi | 01,12,16 |
| D05 tinggi: role alias/proxy | `User.php`, `EnsureUserRole.php`, `RoleMiddleware.php`, `Admin/ProxyController.php` | Tiga role, mapping eksplisit, unknown ditahan; proxy/switchback tidak ada | 01,04,22 |
| D06 tinggi: review membuat consent | `Asesor/AplReviewController.php`, `Apl02ReviewController.php`, ruang uji | Asesor tidak dapat menyetujui signature/AK01 asesi; revocation tetap berlaku | 08,11 |
| D07 tinggi: certificate tanpa keputusan | `PdfDocumentController::sertifikat`, dokumen-muk, `/sertifikat/{id}` | No generator; upload per assessment+decision, hasMany private certificates | 01,21 |
| D08 tinggi: biodata/domain ganda | `AssessmentApplication`/`AssessmentSchedule`/`CertificationScheme`/`AsesiProfile` versus canonical | Reconciliation dry-run, mapping dan conflict quarantine; tidak merge berdasarkan integer ID | 03,22 |
| D09 tinggi: asesor lain pada jadwal sama bisa mengedit | controller asesor dan pivot jadwal | Assigned assessor_id per assessment; test dua asesor satu jadwal | 04,07,16 |
| D10 tinggi: materi tidak lengkap/bercampur | `database/seeders/data/SKM-*.json`, `SchemeInstrumentService.php`, views instrumen | Source/version registry; original tidak lengkap memblokir publish; kunci jawaban tidak ke asesi | 02,05,09–15 |
| D11 tinggi: histori mengikuti perubahan profile | `Assessment`, `UserProfile`, `Scheme` | Immutable snapshots; versioned signature; payload >64KB tidak terpotong TEXT | 03,07,08 |
| D12 tinggi: hasil/report tertimpa | finalization, AK05/06, `PlenoMeeting` | Per jadwal+asesor+revision; optimistic locking; keputusan independen dari asesor | 16,18,20 |
| D13 sedang: field paper hilang | profile/APL/MAPA/AK/IA models/views | Kontak rumah/HP/kantor/fax/email/postal; MAPA group/method/context; AK06 matrix; report-sheet measurements sesuai source | 02,07–19 |
| D14 tinggi: BA/attendance rekaan | jadwal/workspace/reports | Waktu aktual, koordinator nyata, absent bukan BK, attendance event nyata, pleno bukan daftar undangan | 11,19,20 |
| D15 sedang: redirect loop/dead end | `CheckAplApproved`, `TrackLastStep`, dashboard/APL/jadwal/workspace | Pure next-action resolver, satu redirect POST; guard tidak mengarah ke halaman yang ditolak guard sama | 06–08,12,23 |
| D16 sedang: input valid ditolak | profile/APL/register validators | NIK/paspor sesuai tipe, alamat manual/warga asing, alternatif syarat; jangan paksa semua jalur syarat atau kerja fiktif | 07,08 |
| D17 tinggi: seed/migration destruktif | `DatabaseSeeder.php`, seed demo, migration unifikasi | Reference idempotent, no known passwords, dry-run/backup/rehearsal/restore | 03,05,24 |
| D18 tinggi: file/DB tidak konsisten | upload/download controllers | Staging/hash/transaction/cleanup; receipt setelah policy dan file available; no path/PII leak pada error | 04,21,24 |
| D19 sedang: UI tidak konsisten | layouts/public/auth/role views, CSS/JS | Shared patterns, self-host fonts, 390/768/1280, accessible errors, focused assessment | 06,23 |
| D20 sedang: PDF pipeline ganda/bloat | PDF controllers, MukExportService, pdf/exports views | Route/engine dihapus; original/data MUK/certificate download dipertahankan | 01,22 |

Test lama yang mengharuskan impersonation atau certificate generation diganti test penolakan route, bukan dihapus sekadar agar suite hijau. Jangan mematikan assertion security ketika fixture lama tidak memenuhi consent/authority yang benar.
