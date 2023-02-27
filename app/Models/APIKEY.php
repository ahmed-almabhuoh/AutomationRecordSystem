<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APIKEY extends Model
{
    use HasFactory;

    const STATUS = ['active', 'disabled'];

    protected $table = 'a_p_i_k_e_y_s';


    // Relations
    public function manager()
    {
        return $this->belongsTo(Manager::class, 'manager_id', 'id');
    }
}
