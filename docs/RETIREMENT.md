# Manifest penghapusan dan cutover

Tujuan: satu domain core dan tiga role. Tahap awal menutup route berisiko; tahap akhir menghapus kode setelah data yang perlu dipertahankan direkonsiliasi. Tidak menghapus original di `docs/reference`, signature/bukti milik pengguna, atau data historis tanpa backup dan kebijakan retensi owner.

## Hapus modul aktif

| Kelompok | File/simbol baseline yang dihapus atau dipangkas | Yang tetap dipertahankan |
|---|---|---|
| Impersonation | `Admin/ProxyController.php`, route proxy/switchback, UI switch user, proxy session helpers dan test yang mewajibkan fitur | Actor audit asli; tidak ada penggantinya |
| Complaint | `Admin/ComplaintController.php`, root `ComplaintController.php`, `Complaint.php`, views/routes complaint/tracking publik | Tidak ada pengganti atau tracking token |
| Surveillance baseline | `root/SurveillanceController.php` (route public surveillance tanpa otentikasi) dan direct clearance tanpa evaluasi | Digantikan oleh alur lean surveilans SOP 04 §4 / SOP 19 (task 21-03): rencana periodik, laporan pemegang sertifikat + bukti, temuan independen (FR.SPS.01) dan keputusan eksplisit (FR.SK.01) |
| Appeals baseline | Logika lama `Admin/AppealController.php` yang mem-bypass investigasi independen atau tanpa checks independensi | Digantikan oleh alur lean banding SOP 07 (task 21-01): intake AK-04 dari asesi (task 17), penugasan tim investigasi independen (FR-SOP-07.1) dan berita acara temuan/rekomendasi (FR-SOP-07.2) |
| Finance | `Admin/FinanceController.php`, `FinancialAccount.php`, `FinancialTransaction.php`, `BankAccount.php`, views/menu/export finance | Tidak ada tagihan/payment prerequisite core |
| Generic letters | `Admin/LetterController.php`, `Letter.php`, views/template surat umum | Reference/metadata SPT, penunjukan komite, keputusan di objek core |
| Messages | `Admin/MessageController.php`, `Asesor/NotificationController.php`, `SystemMessage.php`, chat/message UI dan routes | Status/error/transaksi pada halaman, bukan inbox |
| Quality | `Admin/QualityController.php`, `AuditInternal.php`, `MutuDocument.php`, views/menu audit mutu | AK-06 dan bukti validasi instrumen SOP05 pada versi materi |
| Generic MKVA/review portal | `Asesor/MkvaController.php`, `InstrumentReviewController.php`, `MkvaAssignment.php`, `InstrumentReview.php` setelah data dipetakan | Validation/trial/approval record perangkat dalam SchemeVersion/AssessmentPlan; jangan menghapus kewajiban SOP05 |
| PDF engine | `PdfDocumentController.php`, `Admin/DokumenMukController.php`, `MukExportService.php`, print-only layout, `resources/views/pdf/**`, `resources/views/exports/**`, route `/sertifikat/{id}` dan export/print | File sertifikat eksternal privat, original reference, data seluruh MUK |

Semua path controller relatif `app/Http/Controllers/`, model relatif `app/Models/`. Task 01/22 menjalankan `rg --files` dan pencarian class/route untuk mencatat semua view/JS/test yang merujuk simbol ini; tidak boleh meninggalkan route tersembunyi karena menu sudah hilang. Dependensi Dompdf dihapus jika tidak ada penggunaan in-scope; PhpSpreadsheet hanya boleh dipertahankan jika import referensi yang benar-benar digunakan memerlukannya. Jangan mengubah composer lokal owner tanpa memisahkan hunk miliknya.

## Legacy domain: migrasi dahulu, hapus runtime kemudian

| Legacy | Target / perlakuan |
|---|---|
| `AssessmentApplication`, `AssessmentSchedule`, `CertificationScheme`, `ScheduleAsesor` | `Assessment`, `Schedule`, `Scheme`, pivot canonical; explicit source-table ID mapping |
| `LegacyCompetencyUnit`, `CompetencyElement`, `WorkKeyCriteria` | Versioned `CompetencyUnit`, `UnitElement`, `Kuk`; konflik isi tidak auto-merge |
| `AsesiProfile` | `UserProfile`, conflict report; jangan mengosongkan data valid yang hanya ada pada legacy |
| `ApplicationDocument`, `Apl02Assessment`, `Apl02Item` | `AssessmentProof`, `AssessmentApl02Item` dan private files |
| `Ak01Agreement`, `Ak02Assessment`, `Ak02UnitAssessment`, `Ak03Feedback`, `Ak05Report` | Canonical typed records; consent lama tanpa bukti eksplisit ditandai unverified, bukan disulap sah |
| `SchemeRequirement` | Pertahankan isi syarat nyata di versi referensi, bukan daftar syarat hard-coded |
| `PlenoMeeting`, `TukVerification`, `AsesorProfile` | **Pertahankan fungsi**, ganti FK legacy setelah mapping, jangan delete karena nama lama |
| `Event` | Jika dipakai batch/jadwal, migrasikan fakta jadwal dahulu; hapus marketing/event module di luar core setelah tidak ada dependensi |
| `Standard` | Pertahankan metadata sumber standar yang dirujuk unit; hapus hanya duplikasi yang telah dipetakan |

Controller legacy `Admin/AssessmentScheduleController`, `CertificationSchemeController`, `VerificationController`, `Asesi/ApplicationController`, `Asesor/ScheduleController` dan `ScheduleController` lainnya harus diperiksa route binding-nya; satukan dengan canonical controller yang melayani tugas sama, kemudian hapus yang tidak digunakan. Jangan memilih implementasi hanya karena nama mirip. Dashboard/profile/APL/jadwal core dipertahankan, direfactor, tidak digandakan ke `/staff`.

## Urutan aman

1. Tutup endpoint tidak aman/out-of-scope dan hapus tombolnya; catat historical data tetap read-only.
2. Backup database+file dan manifest hash. Migration additive untuk canonical relations/mapping; dry-run melaporkan jumlah per tabel, orphans, duplicate candidates/schedules/nomor, unresolved conflicts.
3. Transaksi per batch dengan checkpoint; exception membuat batch rollback, bukan success parsial tanpa laporan. Tidak menebak ID atau mengubah hasil lama menjadi kompeten.
4. Verifikasi counts, FK, attachment hash, ownership dan laporan konflik; unresolved menghalangi cutover record terkait. Maintenance window bila write freeze diperlukan, jangan dual-write tanpa desain.
5. Ganti runtime ke canonical, hilangkan fallback, jalankan collision/authorization/regression tests.
6. Hapus legacy code/routes/views/dependencies setelah tidak ada caller. **Jangan drop tabel historis pada release ini**; dokumentasikan retention dan export/restore. Migration history tetap dapat upgrade DB lama; tidak edit/drop migration lama demi schema baru yang terlihat bersih.
7. Rollback sebelum destructive cleanup: revert release + restore DB/file snapshot konsisten. Setelah ada write baru, rollback memerlukan replay/mapping revision; jangan menjalankan migration down yang membuang data.
