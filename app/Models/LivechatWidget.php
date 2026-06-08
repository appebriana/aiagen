<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LivechatWidget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'department_id', 
        'name', 
        'token', 
        'is_active', 
        'primary_color', 
        'welcome_message',
        'target_domain'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($widget) {
            if (!$widget->token) {
                $widget->token = Str::random(32);
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
