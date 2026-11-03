<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Scheme Versions
        if (! Schema::hasTable('m_scheme_versions')) {
            Schema::create('m_scheme_versions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('scheme_id');
                $table->string('version', 20);
                $table->string('code', 50);
                $table->string('title', 255);
                $table->string('status', 20)->default('draft'); // draft, approved, retired
                $table->string('source_hash', 64)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['scheme_id', 'version']);
            });
        }

        // 2. Legacy Identity Maps
        if (! Schema::hasTable('t_legacy_identity_maps')) {
            Schema::create('t_legacy_identity_maps', function (Blueprint $table) {
                $table->id();
                $table->string('source_table', 100);
                $table->unsignedBigInteger('legacy_id');
                $table->string('target_table', 100);
                $table->unsignedBigInteger('target_id')->nullable();
                $table->string('checksum', 64)->nullable();
                $table->string('status', 30)->default('mapped'); // mapped, conflict, quarantined
                $table->text('reason')->nullable();
                $table->unsignedBigInteger('reconciler_id')->nullable();
                $table->timestamps();

                $table->unique(['source_table', 'legacy_id']);
                $table->index(['target_table', 'target_id']);
            });
        }

        // 3. Private Files
        if (! Schema::hasTable('t_private_files')) {
            Schema::create('t_private_files', function (Blueprint $table) {
                $table->id();
                $table->string('disk', 50)->default('private');
                $table->string('path', 500);
                $table->string('original_name', 255);
                $table->string('mime_type', 100);
                $table->unsignedBigInteger('file_size');
                $table->string('file_hash', 64);
                $table->string('owner_type', 100);
                $table->unsignedBigInteger('owner_id');
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->string('status', 30)->default('available');
                $table->timestamps();

                $table->index(['owner_type', 'owner_id']);
            });
        }

        // 4. Schedule Consents
        if (! Schema::hasTable('t_schedule_consents')) {
            Schema::create('t_schedule_consents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('schedule_id');
                $table->unsignedBigInteger('user_id');
                $table->string('purpose', 50);
                $table->string('text_hash', 64);
                $table->longText('signature_data')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('granted_at')->nullable();
                $table->timestamp('revoked_at')->nullable();
                $table->timestamps();

                $table->index(['schedule_id', 'user_id', 'purpose']);
            });
        }

        // 5. Attendance Events
        if (! Schema::hasTable('t_attendance_events')) {
            Schema::create('t_attendance_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('schedule_id');
                $table->unsignedBigInteger('user_id');
                $table->string('session_code', 50)->nullable();
                $table->string('event_type', 50);
                $table->string('source_record', 100)->nullable();
                $table->timestamp('occurred_at');
                $table->unsignedBigInteger('recorded_by')->nullable();
                $table->timestamps();

                $table->index(['schedule_id', 'user_id']);
            });
        }

        // 6. Certification Decisions
        if (! Schema::hasTable('t_certification_decisions')) {
            Schema::create('t_certification_decisions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('assessment_id');
                $table->unsignedBigInteger('meeting_id')->nullable();
                $table->string('decision', 50); // APPROVED, REJECTED, RETURNED
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamp('signed_at')->nullable();
                $table->string('certificate_number', 100)->nullable();
                $table->timestamps();

                $table->index('assessment_id');
            });
        }

        // 7. Certificates
        if (! Schema::hasTable('t_certificates')) {
            Schema::create('t_certificates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('assessment_id');
                $table->unsignedBigInteger('decision_id')->nullable();
                $table->string('certificate_number', 100)->unique();
                $table->string('blanko_number', 100)->nullable();
                $table->date('issue_date')->nullable();
                $table->date('valid_from')->nullable();
                $table->date('valid_until')->nullable();
                $table->string('private_file_path', 500)->nullable();
                $table->string('file_hash', 64)->nullable();
                $table->unsignedBigInteger('uploader_id')->nullable();
                $table->string('status', 30)->default('ACTIVE'); // ACTIVE, REVOKED, SUPERSEDED
                $table->integer('revision')->default(1);
                $table->timestamps();

                $table->index('assessment_id');
            });
        }

        // 8. Certificate Receipt Events
        if (! Schema::hasTable('t_certificate_receipt_events')) {
            Schema::create('t_certificate_receipt_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('certificate_id');
                $table->unsignedBigInteger('assessment_id');
                $table->unsignedBigInteger('recipient_id');
                $table->string('event_type', 30); // upload, download, acknowledge
                $table->timestamp('occurred_at');
                $table->longText('signature_data')->nullable();
                $table->timestamps();

                $table->index(['certificate_id', 'recipient_id']);
            });
        }

        // 9. Audit Events
        if (! Schema::hasTable('t_audit_events')) {
            Schema::create('t_audit_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action', 100);
                $table->string('subject_type', 100);
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->longText('before_payload')->nullable();
                $table->longText('after_payload')->nullable();
                $table->text('reason')->nullable();
                $table->string('request_id', 100)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index(['subject_type', 'subject_id']);
            });
        }

        // 10. Update t_assessments
        if (Schema::hasTable('t_assessments')) {
            Schema::table('t_assessments', function (Blueprint $table) {
                if (! Schema::hasColumn('t_assessments', 'prior_certificate_id')) {
                    $table->unsignedBigInteger('prior_certificate_id')->nullable()->after('assessor_id');
                }
                if (! Schema::hasColumn('t_assessments', 'snapshot_profile')) {
                    $table->longText('snapshot_profile')->nullable()->after('assessor_notes');
                }
                if (! Schema::hasColumn('t_assessments', 'lock_version')) {
                    $table->integer('lock_version')->default(1)->after('status');
                }
            });
        }

        // 11. Update m_user_profiles
        if (Schema::hasTable('m_user_profiles')) {
            Schema::table('m_user_profiles', function (Blueprint $table) {
                if (! Schema::hasColumn('m_user_profiles', 'signature_version')) {
                    $table->integer('signature_version')->default(1)->after('signature_data');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_certificate_receipt_events');
        Schema::dropIfExists('t_certificates');
        Schema::dropIfExists('t_certification_decisions');
        Schema::dropIfExists('t_attendance_events');
        Schema::dropIfExists('t_schedule_consents');
        Schema::dropIfExists('t_private_files');
        Schema::dropIfExists('t_legacy_identity_maps');
        Schema::dropIfExists('m_scheme_versions');
        Schema::dropIfExists('t_audit_events');
    }
};
