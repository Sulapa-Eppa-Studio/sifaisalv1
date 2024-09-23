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
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_number', 'contract_number');
    }
}
