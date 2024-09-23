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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number');
            $table->string('request_number'); // Request number
            $table->string('payment_stage'); // Payment stage (e.g., "Down Payment", "Phase I", etc.)
            $table->integer('payment_value'); // Payment value (physical progress)
            $table->text('payment_description'); // Brief description of the payment request
            $table->enum('verification_progress', ['ppk', 'ppspm', 'treasurer', 'kpa'])->default('ppk'); // Verification progress
            $table->enum('ppk_verification_status', ['in_progress', 'approved', 'rejected'])->default('in_progress'); // Verification status (default: in progress)
            $table->text('ppk_rejection_reason')->nullable(); // Reason if rejected
            $table->foreignId('ppk_id')->nullable()->constrained('p_p_k_s')->onDelete('set null');
            $table->enum('ppspm_verification_status', ['not_available', 'in_progress', 'approved', 'rejected'])->default('in_progress'); // Verification status (default: in progress)
            $table->text('ppspm_rejection_reason')->nullable(); // Reason if rejected
            $table->foreignId('ppspm_id')->nullable()->constrained('s_p_m_s')->onDelete('set null');
            $table->enum('treasurer_verification_status', ['not_available', 'in_progress', 'approved', 'rejected'])->default('in_progress'); // Verification status (default: in progress)
            $table->text('treasurer_rejection_reason')->nullable(); // Reason if rejected
            $table->foreignId('treasurer_id')->nullable()->constrained('treasurers')->onDelete('set null');
            $table->enum('kpa_verification_status', ['not_available', 'in_progress', 'approved', 'rejected'])->default('in_progress'); // Verification status (default: in progress)
            $table->text('kpa_rejection_reason')->nullable(); // Reason if rejected
            $table->foreignId('kpa_id')->nullable()->constrained('k_p_a_s')->onDelete('set null');
            $table->timestamps();

            $table->foreign('contract_number')->references('contract_number')->on('contracts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
