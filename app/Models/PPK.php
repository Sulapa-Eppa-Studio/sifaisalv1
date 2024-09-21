<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPK extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function workPackages()
    {
        return $this->morphToMany(WorkPackage::class, 'model', 'role_has_work_packages', 'model_id', 'work_package');
    }
}
