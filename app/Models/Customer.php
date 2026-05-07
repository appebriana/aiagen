<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'name',
        'is_muted'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
