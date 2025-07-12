<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conditioning extends Model
{
    use HasFactory;
     // Specify the fields that are mass assignable
     protected $fillable = [
        'rounds',
        'category_id',
        'workout_id',
        'workoutname',
        'reps',
        'time_to_complete',
        'weight',
        'date',
        'unit', 
        'amrap',
        'is_assigned',
    ];
    // public function category()
    // {
    //     return $this->belongsTo(CategoryOption::class);
    // }
    public function category()
    {
        return $this->belongsTo(CategoryOption::class, 'category_id');
    }

    // Define the relationship with the WorkoutLibrary model
    public function workout()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_id');
    }

    public function workouts()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_id');
    }
}
