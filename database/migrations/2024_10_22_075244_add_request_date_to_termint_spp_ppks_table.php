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
        Schema::table('termint_spp_ppks', function (Blueprint $table) {
            $table->date('spp_date')->nullable()->after('no_termint'); // Menambahkan kolom request_date setelah request_number
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('termint_spp_ppks', function (Blueprint $table) {
            $table->dropColumn('spp_date'); // Menghapus kolom request_date jika migrasi di-rollback
        });
    }
};
