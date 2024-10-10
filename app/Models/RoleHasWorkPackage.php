<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleHasWorkPackage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function workPackage()
    {
        return $this->belongsTo(WorkPackage::class, 'work_package_id', 'id');
    }
}
