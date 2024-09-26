<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPMRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'spm_id',
        'spm_number',
        'spm_value',
        'spm_description',
        'status',
        'rejection_reason',
        'created_by',
        'spm_document',
        'ppk_request_id',
        'payment_request_id',
        'ppk_id'
    ];

    // Relasi dengan SPM
    public function spm()
    {
        return $this->belongsTo(SPM::class);
    }

    // Relasi dengan User (pengguna yang mengajukan)
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payment_request()
    {
        return $this->belongsTo(PaymentRequest::class, 'payment_request_id');
    }

    public function ppk_request()
    {
        return $this->belongsTo(TermintSppPpk::class, 'ppk_request_id');
    }
}
