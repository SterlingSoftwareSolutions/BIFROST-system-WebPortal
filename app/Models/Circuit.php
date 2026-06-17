<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Circuit extends Model
{
    use HasFactory;

    protected $table = 'circuit';

    protected $fillable = [
        'workout_manager_id',
        'workout_libraries_id',
        'stationumber',
        'training_load',
        'unit_type',
        'reps',
        'gender',
        'is_for_time',
        'time_to_complete',
    ];
    public function workoutManager()
    {
        return $this->belongsTo(WorkoutManager::class);
    }

    public function workoutLibrary()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_libraries_id');
    }
}
