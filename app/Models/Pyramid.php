<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pyramid extends Model
{
    use HasFactory;

    protected $table = 'pyramid';

    protected $fillable = [
        'workout_manager_id',
        'workout_libraries_id',
        'training_load',
        'unit_type',
        'reps',
        'gender',
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
