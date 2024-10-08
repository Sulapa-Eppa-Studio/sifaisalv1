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
        Schema::create('role_has_work_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_package_id');
            $table->unsignedBigInteger('role_id'); // ID dari model yang terkait
            $table->string('role_type'); // Nama model (PPK, ServicesProvider, dll)
            $table->timestamps();

            $table->foreign('work_package_id')->references('id')->on('work_packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_has_work_packages');
    }
};
