<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Straight extends Model
{
    use HasFactory;

    protected $table = 'straight';

    protected $fillable = [
        'workout_manager_id',
        'workout_libraries_id',
        'training_load',
        'unit_type',
        'reps',
    ];

    public function sets()
    {
        return $this->hasMany(StraightSet::class, 'straight_id');
    }

    public function workoutManager()
    {
        return $this->belongsTo(WorkoutManager::class);
    }

    public function workoutLibrary()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_libraries_id');
    }
}
