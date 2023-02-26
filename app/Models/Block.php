<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    // Attributes
    const POSITIONS = [
        'manager', 'admin', 'supervisor', 'keeper', 'student_parent', 'student',
    ];
    const STATUS = [
        'active', 'disable'
    ];

    // Attributes
    public function getBlockStatusClassAttribute()
    {
        $class = 'label font-weight-bold label-lg  label-light-success label-inline';
        if ($this->status === 'active') {
            $class = 'label font-weight-bold label-lg  label-light-danger label-inline';
        } else if ($this->status === 'inactive') {
            $class = 'label font-weight-bold label-lg  label-light-info label-inline';
        }
        return $class;
    }


    // Relations
    public function manager()
    {
        return $this->belongsTo(Manager::class, 'blocked_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'blocked_id', 'id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'blocked_id', 'id');
    }

    public function parents()
    {
        return $this->belongsTo(StudentParent::class, 'blocked_id', 'id');
    }

    public function students()
    {
        return $this->belongsTo(Student::class, 'blocked_id', 'id');
    }
}
