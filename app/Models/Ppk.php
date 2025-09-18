<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPK extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'ppk_id', 'id');
    }

    // Di model PPK
    public function workPackages()
    {
        return $this->morphToMany(WorkPackage::class, 'role', 'role_has_work_packages');
    }
}
