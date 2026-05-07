<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description', 'ai_name', 'ai_job_description', 'reply_to_groups', 'is_24_hours', 'open_time', 'close_time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function knowledgeFiles()
    {
        return $this->hasMany(KnowledgeFile::class);
    }

    public function whatsappDevices()
    {
        return $this->hasMany(WhatsappDevice::class);
    }
}
