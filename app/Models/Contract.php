<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    public $guarded = [];

    public function admin() {}

    public function service_provider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }
  
    public function termintSppPpks()
    {
        return $this->hasMany(TermintSppPpk::class);
    }
}
