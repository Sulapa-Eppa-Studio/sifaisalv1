<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    public $guarded = [];

    public function admin() {}

    public function termintSppPpks()
    {
        return $this->hasMany(TermintSppPpk::class);
    }
}
