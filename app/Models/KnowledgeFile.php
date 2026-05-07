<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
