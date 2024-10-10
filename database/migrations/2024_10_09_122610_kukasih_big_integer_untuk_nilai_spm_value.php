<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
      /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('s_p_m_requests', function (Blueprint $table) {
            $table->bigInteger('spm_value')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('s_p_m_requests', function (Blueprint $table) {
            // Pastikan tipe kolom yang sebelumnya (misalnya integer)
            $table->integer('spm_value')->change();
        });
    }
};
