<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    // Attributes
    const POSITIONS = [
        'manager'
    ];
    const STATUS = [
        'active', 'disable'
    ];


    // Relations
    public function manager()
    {
        return $this->belongsTo(Manager::class, 'blocked_id', 'id');
    }
}