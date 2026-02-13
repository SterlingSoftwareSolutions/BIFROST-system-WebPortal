<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserScore extends Model
{
    protected $fillable = [
        'user_id',
        'selected_day',
        'sleep_input',
        'alertness_input',
        'excitement_input',
        'stress_input',
        'soreness_input',
        'score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    use HasFactory;
    
}
