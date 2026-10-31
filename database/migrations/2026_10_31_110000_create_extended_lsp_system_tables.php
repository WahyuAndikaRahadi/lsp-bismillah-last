<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Audit Internal LSP
        Schema::create('audit_internals', function (Blueprint $table) {
            $table->id();
            $table->string('audit_code')->unique();
            $table->date('audit_date');
            $table->string('auditor_name');
            $table->text('scope');
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('status')->default('rencana'); // rencana, selesai, ditutup
            $table->timestamps();
        });

        // 2. Dokumen Mutu LSP
        Schema::create('mutu_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_code')->unique();
            $table->string('title');
            $table->string('category'); // panduan_mutu, sop, formulir, renstra
            $table->string('version')->default('1.0');
            $table->date('effective_date');
            $table->string('file_path')->nullable();
            $table->string('status')->default('aktif'); // aktif, revisi, usang
            $table->timestamps();
        });

        // 3. Persuratan Resmi LSP
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->string('category'); // surat_tugas, surat_keputusan, surat_edaran, surat_undangan, surat_perjanjian, dll
            $table->string('subject');
            $table->string('recipient');
            $table->date('letter_date');
            $table->text('content')->nullable();
            $table->string('signer_name')->nullable();
            $table->string('signer_role')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status')->default('terbit'); // draft, terbit, diarsipkan
            $table->timestamps();
        });

        // 4. Rekening Bank LSP
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_holder');
            $table->string('branch')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Kode Akun Keuangan
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code')->unique();
            $table->string('account_name');
            $table->string('account_type'); // aktiva, kewajiban, modal, pendapatan, beban
            $table->timestamps();
        });

        // 6. Transaksi Keuangan LSP
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('financial_accounts')->cascadeOnDelete();
            $table->date('transaction_date');
            $table->string('reference_type')->nullable(); // pembayaran_asesi, honor_asesor, operasional, dll
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description');
            $table->enum('type', ['pemasukan', 'pengeluaran']);
            $table->decimal('amount', 15, 2);
            $table->string('proof_file')->nullable();
            $table->timestamps();
        });

        // 7. Log Pesan / SMS / WA Notifikasi
        Schema::create('system_messages', function (Blueprint $table) {
            $table->id();
            $table->enum('direction', ['outgoing', 'incoming'])->default('outgoing');
            $table->string('channel')->default('sms'); // sms, whatsapp, email
            $table->string('recipient_or_sender');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('status')->default('terkirim'); // terkirim, gagal, diterima
            $table->timestamps();
        });

        // 8. Verifikasi Sarana Prasarana TUK oleh Asesor
        Schema::create('tuk_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuk_id')->constrained('tuks')->cascadeOnDelete();
            $table->foreignId('asesor_id')->constrained('asesor_profiles')->cascadeOnDelete();
            $table->date('verification_date');
            $table->string('verification_type')->default('mandiri'); // sewaktu, mandiri, tempat_kerja
            $table->json('facility_checklist')->nullable();
            $table->text('notes')->nullable();
            $table->enum('recommendation', ['layak', 'belum_layak'])->default('layak');
            $table->string('signature_path')->nullable();
            $table->string('status')->default('diverifikasi'); // pending, diverifikasi, disetujui
            $table->timestamps();
        });

        // 9. Peninjauan Instrumen / Perangkat Asesmen oleh Asesor
        Schema::create('instrument_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')->constrained('certification_schemes')->cascadeOnDelete();
            $table->foreignId('asesor_id')->constrained('asesor_profiles')->cascadeOnDelete();
            $table->string('instrument_type'); // mapa_01, mapa_02, soal_tulis, soal_praktik, dpt
            $table->date('review_date');
            $table->json('review_criteria')->nullable(); // validitas, reliabilitas, fleksibilitas, keadilan
            $table->text('notes')->nullable();
            $table->enum('recommendation', ['disetujui', 'perlu_perbaikan'])->default('disetujui');
            $table->string('status')->default('disetujui');
            $table->timestamps();
        });

        // 10. Penugasan MKVA (Meninjau dan Mengembangkan Perangkat Asesmen)
        Schema::create('mkva_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')->constrained('certification_schemes')->cascadeOnDelete();
            $table->foreignId('asesor_id')->constrained('asesor_profiles')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('assessment_schedules')->nullOnDelete();
            $table->date('assignment_date');
            $table->json('review_aspects')->nullable();
            $table->text('findings')->nullable();
            $table->text('improvement_plan')->nullable();
            $table->string('status')->default('selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mkva_assignments');
        Schema::dropIfExists('instrument_reviews');
        Schema::dropIfExists('tuk_verifications');
        Schema::dropIfExists('system_messages');
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('financial_accounts');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('letters');
        Schema::dropIfExists('mutu_documents');
        Schema::dropIfExists('audit_internals');
    }
};
