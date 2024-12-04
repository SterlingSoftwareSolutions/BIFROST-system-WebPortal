<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warmup extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'workout_id',
        'reps',
        'weight',
        'date'
    ];


    public function workouts()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_id');
    }



    public function category()
    {
        return $this->belongsTo(CategoryOption::class, 'category_id');
    }

    // Define the relationship to the WorkoutLibrary model
    public function workout()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_id');
    }
    public function dailyWarmups()
    {
        return $this->hasMany(DailyWarmup::class, 'warmup_id');
    }
}
