<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'contract_number',
        'request_number',
        'payment_stage',
        'payment_value',
        'payment_description',
        'verification_status',
        'rejection_reason',
        'service_provider_id',
        'verification_progress',
        'ppk_verification_status',
        'ppk_rejection_reason',
        'ppk_id',
        'ppspm_verification_status',
        'ppspm_rejection_reason',
        'ppspm_id',
        'treasurer_verification_status',
        'treasurer_rejection_reason',
        'treasurer_id',
        'kpa_verification_status',
        'kpa_rejection_reason',
        'kpa_id',
    ];

    protected $with = ['ppk', 'spm', 'treasurer', 'kpa', 'contract'];

    protected $casts = [
        'payment_value' =>  'integer',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_number', 'contract_number');
    }

    public function service_provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id', 'id');
    }

    public function ppk()
    {
        return $this->belongsTo(PPK::class, 'ppk_id', 'id');
    }

    public function spm()
    {
        return $this->belongsTo(SPM::class, 'ppspm_id', 'id');
    }

    public function treasurer()
    {
        return $this->belongsTo(Treasurer::class, 'treasurer_id', 'id');
    }

    public function kpa()
    {
        return $this->belongsTo(KPA::class, 'kpa_id', 'id');
    }
}
