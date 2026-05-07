<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'customer_phone',
        'question',
        'answer',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'cost'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
