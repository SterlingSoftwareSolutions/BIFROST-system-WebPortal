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
        'workoutname',
        'reps',
        'weight',
        'is_assigned',
        'unit',
        'male',
        'female',
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

    public static function store($data)
{
    $warmup = new self();

    $warmup->category_id = $data['category_id'] ?? null;
    $warmup->workout_id = $data['workout_id'] ?? null;
    $warmup->workoutname = $data['workoutname'] ?? null;
    $warmup->reps = $data['reps'] ?? null;
    $warmup->weight = $data['weight'] ?? null;
    $warmup->unit = $data['unit'] ?? null;
    $warmup->male = $data['male'] ?? null;
    $warmup->female = $data['female'] ?? null;
    $warmup->date = $data['date'] ?? null;
    $warmup->is_assigned = $data['is_assigned'] ?? false;

    $warmup->save();

    return $warmup;
}
}
