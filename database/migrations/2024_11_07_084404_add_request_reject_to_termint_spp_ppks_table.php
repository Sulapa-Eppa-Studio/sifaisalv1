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
            $table->boolean('request_reject')->nullable()->default(0)->after('ppspm_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('termint_spp_ppks', function (Blueprint $table) {
            $table->dropColumn('request_reject');
        });
    }
};
