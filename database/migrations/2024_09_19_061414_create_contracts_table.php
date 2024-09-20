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
            $table->string('service_provider'); // Penyedia Jasa
            $table->string('npwp', 20); // NPWP
            $table->string('bank_account_number', 20); // No. Rekening
            $table->string('ppk_officer'); // Pejabat Pembuat Komitmen
            $table->string('working_unit'); // Satuan Kerja
            $table->timestamps();
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
