<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermintSppPpkFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'termint_spp_ppk_id',
        'file_type',
        'file_path',
    ];

    public function termintSppPpk()
    {
        return $this->belongsTo(TermintSppPpk::class);
    }
}
