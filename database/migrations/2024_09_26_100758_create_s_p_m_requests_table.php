<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('s_p_m_requests', function (Blueprint $table) {
            $table->id();
            $table->string('spm_number')->unique(); // Nomor SPM
            $table->string('spm_description'); // Uraian pembayaran SPM
            $table->string('spm_document'); // File Surat Perintah Membayar yang diupload
            $table->integer('spm_value'); // Nilai SPM (nilai SPP dikurangi PPN dan PPH)
            $table->unsignedBigInteger('spm_id'); // Relasi ke tabel SPM
            $table->unsignedBigInteger('ppk_request_id'); //    User ID yang mengajukan pengajuan
            $table->unsignedBigInteger('payment_request_id');
            $table->enum('treasurer_verification_status', ['not_available', 'in_progress', 'approved', 'rejected'])->default('in_progress'); // Verification status (default: in progress)
            $table->text('treasurer_rejection_reason')->nullable(); // Reason if rejected
            $table->foreignId('treasurer_id')->nullable()->constrained('treasurers')->onDelete('set null');
            $table->enum('kpa_verification_status', ['not_available', 'in_progress', 'approved', 'rejected'])->default('not_available'); // Verification status (default: in progress)
            $table->text('kpa_rejection_reason')->nullable(); // Reason if rejected
            $table->timestamps(); // Timestamps for created_at and updated_at

            // Foreign keys
            $table->foreign('spm_id')->references('id')->on('s_p_m_s')->onDelete('cascade'); // Relasi ke tabel SPM
            $table->foreign('ppk_request_id')->references('id')->on('termint_spp_ppks')->onDelete('cascade'); // Relasi ke tabel Users
            $table->foreign('payment_request_id')->references('id')->on('payment_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_p_m_requests');
    }
};
