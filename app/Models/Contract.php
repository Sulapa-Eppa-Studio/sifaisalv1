<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'paid_value'    =>  'integer',
        'payment_value' =>  'integer',
    ];

    public function admin() {}

    public function service_provider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function termintSppPpks()
    {
        return $this->hasMany(TermintSppPpk::class);
    }

    public function payment_request()
    {
        return $this->hasMany(PaymentRequest::class, 'contract_number', 'contract_number');
    }
}
