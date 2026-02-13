<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'goal_name',
        'specific',
        'measurable',
        'achievable',
        'relevant',
        'time_bound',
        'achievable_progress',
        'time_progress',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
