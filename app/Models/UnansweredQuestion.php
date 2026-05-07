<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnansweredQuestion extends Model
{
    protected $fillable = [
        'department_id',
        'sender',
        'question',
        'answer',
        'is_answered'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function customer()
    {
        // Mencari customer berdasarkan nomor pengirim (sender) 
        // dan ID pemilik departemen (user_id)
        return $this->hasOne(Customer::class, 'phone', 'sender')
                    ->where('user_id', $this->department->user_id ?? 0);
    }
}
