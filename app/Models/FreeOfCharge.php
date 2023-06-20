<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeOfCharge extends Model
{
    const FOC_TYPE_1 = 1;
    const FOC_TYPE_2 = 2;
    const FOC_TYPE_3 = 3;

    protected $fillable = [
        'user_id',
        'product_id',
        'type',
        'foc_2_val',
        'foc_3_val'
    ];

    public function users() {
        return $this->belongsToMany(User::class);
    }
}
