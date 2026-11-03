# Kontrak model dan transisi target

Ini desain yang harus diimplementasikan, bukan schema baseline yang sudah tersedia. Gunakan nama fisik domain core `m_*`/`t_*` yang sudah ada; jangan mengganti nama tabel hanya untuk kosmetik. Kolom baru melalui migration additive, bukan mengedit migration yang pernah dipakai.

## Satu domain

| Entitas target | Relasi dan data wajib | Pemilik / batas perubahan |
|---|---|---|
| `User`, `UserProfile` | Role canonical; identitas, kontak pribadi/pekerjaan/pendidikan sesuai APL-01; signature version privat | User sendiri; koreksi admin beralasan, bukan impersonation |
| `AsesorProfile` | User, MET, kompetensi/skema yang berwenang, masa berlaku credential, bukti | Admin memverifikasi; jangan menghapus credential saat merapikan profil |
| `LspProfile`, `Setting` | Identitas legal, logo publik aman, warna CSS tervalidasi, timezone, nomor dokumen, pejabat sah | Konfigurasi satu LSP, tidak ada tenant_id |
| `Scheme` + `SchemeVersion` baru | Kode, judul, nomor resmi, kemasan, source/hash, status draft/approved/retired; unit/elemen/KUK/kelompok/requirements/instrumen/soal versi itu | Versi approved immutable; perubahan menjadi versi baru |
| `FormDefinition` baru | Kode semantic, kode cetak asli, source/page/hash, urutan section/row/field, label verbatim, tipe input, opsi asli, applicability | Registry tetap untuk MUK, **bukan** no-code form/workflow builder |
| `Tuk`, `TukVerification` | Jenis, alamat, ruang/peralatan, hasil verifikasi nyata, verifier, tanggal, dasar SPT/penetapan, evidence | TUK yang belum verified tidak boleh dipakai publish sesi |
| `Schedule` | SchemeVersion, TUK, tanggal/rentang sesi/timezone, kapasitas, koordinator nyata, rujukan penunjukan, nomor kegiatan | Admin; perubahan setelah consent memerlukan revisi/acknowledgment terkait |
| Pivot asesor jadwal | Assessor user, penunjukan, masa tugas, credential snapshot, acceptance | Menentukan siapa boleh ditugaskan, bukan akses bebas semua peserta |
| `Assessment` | Candidate user, schedule, scheme version, assigned assessor, stage/revision, snapshot profile/reference, actual start/end | Satu assessment per candidate+schedule; asesmen berikutnya = record baru |
| `AssessmentProof`, `AssessmentApl02Item` | Requirement/KUK version ID, file/referensi evidence, self K/BK, reviewer status/rationale | Nilai asesi dan nilai reviewer terpisah; semua FK terikat versi assessment |
| `AssessmentPlan` baru | Schedule+assessor+revision; MAPA-01/02, work groups, metode, instruments, review/validation evidence | Asesor menyusun; pengesahan oleh reviewer berwenang dan identitas nyata |
| `AssessmentAk07Item`, `AssessmentIaResponse` | Data terstruktur sesuai registry, row ID versi asli; kebutuhan/observasi/jawaban terpisah, capture actor/time | Asesi hanya kolom miliknya; asesor hanya objek ditugaskan |
| `AssessmentAk02Item` | Unit ID, sumber evidence, penilaian asesor eksplisit, alasan/followup; rekomendasi keseluruhan terpisah | Tidak ada default K, bulk auto-K, atau CBT→K |
| `AssessmentAk03Item`, `AssessmentAppeal` (AK-04) | Jawaban/komentar asesi menurut original; pengajuan AK-04 alasan, unit yang dibantah, signature asesi | Intake resmi banding (task 17) menjadi masukan investigasi independen (task 21-01) |
| `AppealInvestigation` baru | Appeal FK, penugasan tim investigasi (FR-SOP-07.1), berita acara temuan & rekomendasi (FR-SOP-07.2), status outcome | Investigator independen (bukan asesor peserta); kerahasiaan terjaga; tidak otomatis mengubah nilai |
| `ImpartialityCommitment`, `ConflictDeclaration`, `AssessorReplacementEvent` baru | Komitmen personil (FR-SOP-01.1/01.4/01.5), deklarasi benturan kepentingan, pergantian asesor resmi (FR-SOP-01.7) | Unresolved conflict memblokir tindakan asesor; pergantian tercatat resmi dengan actor, alasan, dan persetujuan |
| `SurveillancePlan`, `SurveillanceReport` baru | Periode surveilans, laporan pemegang sertifikat + bukti (FR.SP.01), temuan evaluasi independen (FR.SPS.01), keputusan (FR.SK.01) | Terikat sertifikat aktif; tidak ada auto-clearance atau pencabutan otomatis |
| `AssessmentReport` baru | Schedule+assessor+revision, AK-05 dan AK-06 terstruktur, participant snapshot, author/reviewer/signature refs | Tidak saling menimpa antara asesor; supersede beralasan |
| `ScheduleMinutes` baru | BA normal, actual times/location/coordinator, daftar participant+hasil+followup, asal agregasi, nomor sumber pending Q1 | Draft agregasi dari data nyata; final eksplisit, tidak lewat GET |
| `ScheduleConsent` baru | Schedule+user+purpose+revision, teks/hash, signature version, granted/revoked actor/time | Satu consent signature aktif per orang/jadwal; AK-01 acceptance tujuan berbeda |
| `AttendanceEvent` baru | Schedule/session+person, event_type, source record, occurred_at, recorded_by, correction/reversal ref | Append-only; unik event aktif per sesi/orang; no invitation→present |
| `PlenoMeeting` + record peserta/penunjukan | Agenda, waktu/tempat aktual, anggota/fungsi/appointment, attendance evidence, notulen, review dan corrective action | Admin core; pengambil keputusan independen dari assessor peserta |
| `CertificationDecision` baru | Assessment, meeting/review revision, hasil disetujui/ditolak/dikembalikan, alasan, pejabat pengesah, waktu, bukti keputusan/nomor | Final immutable; reversal/supersession eksplisit, tidak mengubah AK-02 |
| `Certificate` baru | Assessment FK, decision FK, nomor, issue/valid dates, blanko, private file/hash, uploader, revision/status | Satu sertifikat aktif per assessment; penggantian mempertahankan versi lama; candidate hasMany melalui assessment |
| `CertificateReceiptEvent` baru | Certificate revision+assessment+actor, jenis upload/download/acknowledge, waktu, consent/signature ref yang sah | FR-10.2 memakai penerimaan nyata; upload bukan penerima telah tanda tangan |
| `PrivateFile` baru | Disk privat, opaque path, original display name, MIME,size,hash, owner/subject type+id, upload actor/time, status | Tidak menerima path/disk dari request; policy objek sebelum stream |
| `AuditEvent` baru | Actor nyata, subject type+id, action, before/after revision/hash, reason, request ID, timestamp | Append-only; tidak menyimpan password, raw signature, full PII/jawaban di log |
| `LegacyIdentityMap` baru | source_table+legacy_id unik, target_table+id, checksum, reconciler/time, decision/evidence | Bukan runtime fallback. Konflik ditahan, tidak menebak berdasarkan ID |

JSON boleh untuk payload tabel formulir yang berbeda, **dengan** registry key/type/allowed values dan validator per form. Jangan menyimpan seluruh proses sebagai satu blob bebas. Foreign IDs dan query-critical fields tetap kolom/relasi. Gunakan JSON/LONGTEXT sesuai engine dan payload maksimum teruji; encrypted snapshot tidak boleh memakai TEXT yang terpotong 64 KB. Jangan mengasumsikan enkripsi database tersedia; lindungi storage, backup, dan key sesuai deployment.

## Transisi eksplisit

Status internal berikut adalah keputusan desain software, bukan label baru yang mengganti wording kertas. UI menerjemahkan status dengan label konsisten. Semua command POST/PATCH, authorization+CSRF+validation+transaction; GET hanya membaca (session teknis bukan mutasi domain).

| Dari → ke | Actor / command | Prasyarat | Tidak boleh |
|---|---|---|---|
| application draft → submitted | Asesi `Kirim permohonan` | APL-01/02 dan bukti wajib lengkap sesuai jalur syarat; consent tercatat | Mengisi self K/BK default atau mewajibkan semua jalur alternatif sekaligus |
| submitted → revision_required / eligible / ineligible | Asesor review; admin administrative record terpisah | Assigned asesor, credential valid, review setiap syarat relevan, alasan bila revisi/tolak | Review sekaligus membuat signature consent atau kompetensi |
| revision_required → submitted | Asesi | Revisi tersimpan, komentar reviewer tetap ada | Mengedit rekomendasi reviewer |
| eligible → ready | Asesor memfinalkan plan; asesi menerima AK-01 | MAPA valid, instrument versi approved, TUK verified, AK-07 terisi, consent kedua pihak | Membuka semua instrumen dengan flag unlocked tanpa rencana |
| ready → in_progress | Asesi `Masuk ruang uji` / asesor mulai sesi sesuai tugas | Hari/rentang sesi benar, assigned, consent/AK-01 masih berlaku | GET membuat hadir; clock klien menentukan batas |
| in_progress → recommended | Asesor `Simpan rekomendasi` | Bukti required metode aktual, IA lengkap, unit finding eksplisit, AK-02 lengkap | Skor teori otomatis menentukan kompeten atau absen=BK |
| recommended → submitted_for_review | Asesor mengirim paket; admin menutup BA aktual | AK-05/06 lengkap, BA/attendance dan lampiran valid, tidak ada konflik revisi | Menutup dengan placeholder atau menimpa laporan asesor lain |
| submitted_for_review → correction_required / certification_approved / certification_rejected | Pengesah berwenang dalam interface admin | Review/pleno aktual, independensi, ketidaksesuaian diselesaikan atau dikembalikan, keputusan nyata | Auto-approve semua rekomendasi K |
| correction_required → submitted_for_review | Actor pemilik record yang dikoreksi | Revisi+alasan, verifikasi koreksi, reviewer tidak mengedit temuan asesor | Menghapus keputusan lama agar lolos |
| certification_approved → certificate_available | Admin upload | Decision sah untuk assessment itu, file dan metadata valid | Generate certificate atau upload milik assessment lain |
| appeal submitted → under_investigation → appeal_resolved | Asesi kirim AK-04; admin tugaskan tim independen (07.1); tim simpan BA (07.2) | Dalam window banding yang aktif, tim independen (bukan asesor kandidat), berita acara temuan nyata | Mengubah kompetensi langsung tanpa proses ulang/keputusan sah |
| conflict_declared → assessor_blocked → assessor_replaced | Asesor deklarasi benturan; admin tugaskan pengganti (01.7) | Deklarasi tercatat, persetujuan Manajer/Direktur, asesor pengganti valid | Asesor berkonflik tetap menilai peserta |
| surveillance scheduled → report_submitted → evaluated → decided | Admin buat jadwal; pemegang sertifikat upload bukti; tim independen evaluasi | Sertifikat aktif pemegang, bukti pemeliharaan valid, keputusan eksplisit (FR.SK.01) | Auto-clearance atau auto-revocation tanpa evaluasi manusia |
| certificate_held → recertification_applied → new_assessment | Pemegang sertifikat ajukan permohonan resertifikasi | Sertifikat sah milik asesi pada skema sama, profil lama digunakan | Mengganti/menghapus riwayat sertifikat terdahulu atau auto-lulus |

`cancelled` hanya command admin beralasan sebelum final decision; tidak menghapus history. Setelah final, gunakan reversal/supersession dan blokir download sertifikat yang dicabut; pengesahan ulang memerlukan keputusan baru. Durasi/reopen CBT adalah akomodasi nyata dengan actor/reason, bukan bypass timer diam-diam.

## Concurrency, error, dan security contract

- Unique constraints: candidate+schedule, source_table+legacy_id, versioned row keys, active consent/event, certificate assessment+revision. Nomor sertifikat unik pada LSP; konflik nomor harus ditolak, bukan suffix buatan.
- `lock_version`/expected revision untuk update form; stale write → 409 dengan draft user tetap tersedia, tidak last-write-wins.
- Idempotency untuk join, consent, check-in, submit, recommendation dan upload; retry tidak menduplikasi outcome. Transaction untuk perubahan DB; file staging sebelum finalize dan kompensasi bila DB/file gagal.
- 401/redirect login untuk tamu; 403/404 sesuai policy tanpa bocor keberadaan objek; 422 menunjuk field sesuai label asli; 409 untuk state/version conflict. Pesan ramah dan request ID; log teknis tanpa PII.
- Private preview/download juga policy; signed URL bukan pengganti pemeriksaan owner/revocation. Answer keys tidak masuk response asesi, HTML, JSON, source bundle atau public reference route.
- Jangan memublikasikan `docs/reference` melalui webserver: berisi kunci jawaban. Public file hanya logo/aset yang telah divalidasi.

Default batas upload produk (bukan field/ketentuan MUK): bukti PDF/JPEG/PNG maksimum 10 MiB per file; sertifikat PDF maksimum 10 MiB; signature PNG maksimum 1 MiB dan 4096×4096 px, decode/re-encode server sebelum menyimpan. Logo PNG/JPEG/WebP maksimum 2 MiB, tanpa SVG/HTML aktif. Gunakan config tunggal untuk ukuran agar UI, validator dan server selaras. Tolak polyglot/extension-MIME mismatch; file privat didownload sebagai attachment. Jangan memasang arbitrary executable converter. Set PHP/nginx upload limit lebih besar dari limit aplikasi agar aplikasi dapat memberi pesan validasi, dan batasi jumlah file per request menurut daftar bukti yang sedang diisi.
