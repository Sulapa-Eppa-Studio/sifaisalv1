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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            $table->string('contract_number')->unique(); // No. Kontrak
            $table->date('contract_date'); // Tgl Kontrak
            $table->string('can_number')->nullable(); // No. CAN
            $table->string('work_package'); // Paket Pekerjaan
            $table->integer('execution_time'); // Masa Pelaksanaan (Hari Kalender)
            $table->boolean('advance_payment')->default(false); // Pemberian Uang Muka (Ya/Tidak)
            $table->integer('payment_stages')->nullable(); // Jumlah Tahap Pembayaran
            $table->string('npwp', 20); // NPWP
            $table->string('bank_account_number', 20); // No. Rekening
            $table->string('working_unit'); // Satuan Kerja
            $table->bigInteger('payment_value'); // Nilai Kontrak
            $table->bigInteger('paid_value')->default(0); // Nilai Kontrak
            $table->foreignId('service_provider_id'); // Penyedia Jasa
            $table->foreignId('admin_id')->nullable();
            $table->foreignId('ppk_id')->nullable();
            // In your contracts migration file

            $table->timestamps();

            $table->foreign('service_provider_id')->references('id')->on('service_providers')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('ppk_id')->references('id')->on('p_p_k_s')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
