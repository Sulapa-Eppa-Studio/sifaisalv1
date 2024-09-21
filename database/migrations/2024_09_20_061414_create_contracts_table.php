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
            $table->integer('payment_stages'); // Jumlah Tahap Pembayaran
            $table->string('npwp', 20); // NPWP
            $table->string('bank_account_number', 20); // No. Rekening
            $table->string('ppk_officer'); // Pejabat Pembuat Komitmen
            $table->string('working_unit'); // Satuan Kerja
            $table->foreignId('service_provider_id'); // Penyedia Jasa
            $table->foreignId('admin_id');
            $table->timestamps();

            $table->foreign('service_provider_id')->references('id')->on('service_providers');
            $table->foreign('admin_id')->references('id')->on('users');
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
