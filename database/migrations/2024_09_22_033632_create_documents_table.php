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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_request_id'); // Foreign key to payments table
            $table->string('name'); // Nama dokumen (e.g., "Surat Permohonan", "NPWP")
            $table->string('type'); // Jenis Dokumen
            $table->string('path'); // Path file dokumen yang diunggah
            $table->timestamps(); // Waktu pembuatan dan update

            // Foreign key constraint
            $table->foreign('payment_request_id')->references('id')->on('payment_requests')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
