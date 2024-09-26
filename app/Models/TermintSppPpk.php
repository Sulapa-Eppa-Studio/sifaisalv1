<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermintSppPpk extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contract_id',
        'no_termint',
        'description',
        'payment_value',
        'has_advance_payment',
        'ppspm_verification_status',
        'ppspm_rejection_reason',
        'ppspm_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function files()
    {
        return $this->hasMany(TermintSppPpkFile::class);
    }
}
