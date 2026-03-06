<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStrength extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'strength_id',
        'reps',
        'date',
        'type',
        'weight',
        'set_number',
        'exercise_time',
        'workout_manager_id',
        'workout_format_type',
        'workout_format_id',
    ];

    public function member()
    {
        return $this->belongsTo(Newprofile::class, 'member_id');
    }

    public function strenght()
    {
        return $this->belongsTo(Strength::class, 'strength_id');
    }

    public function workoutManager()
    {
        return $this->belongsTo(WorkoutManager::class, 'workout_manager_id');
    }

    public function workoutFormat()
    {
        return $this->morphTo('workout_format', 'workout_format_type', 'workout_format_id');
    }

}
