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
        Schema::table('termint_spp_ppks', function (Blueprint $table) {
            $table->foreignId('payment_request_id')->nullable()->constrained('payment_requests')->onDelete('set null')->after(column: 'contract_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('termint_spp_ppks', function (Blueprint $table) {
            $table->dropColumn('payment_request_id');
        });
    }
};
