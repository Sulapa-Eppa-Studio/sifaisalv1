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
        Schema::create('termint_spp_ppks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('no_termint');
            $table->string('description');
            $table->string('payment_value');
            $table->boolean('has_advance_payment')->default(false);
            $table->enum('ppspm_verification_status', ['not_available', 'in_progress', 'approved', 'rejected'])->default('in_progress'); // Verification status (default: in progress)
            $table->text('ppspm_rejection_reason')->nullable(); // Reason if rejected
            $table->foreignId('ppspm_id')->nullable()->constrained('s_p_m_s')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('termint_spp_ppks');
    }
};
