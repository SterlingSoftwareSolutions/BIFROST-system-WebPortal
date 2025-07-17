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
        'Pyramid',
        'intensity',
        'male',
        'female',
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
    public function pyramidSets()
    {
        return $this->hasMany(PyramidSet::class);
    }
    public static function store($data)
    {
        $conditioning = new self();

        $conditioning->category_id = $data['category_id'] ?? null;
        $conditioning->workout_id = $data['workout_id'] ?? null;
        $conditioning->workoutname = $data['workoutname'] ?? null;
        $conditioning->reps = $data['reps'] ?? null;
        $conditioning->time_to_complete = $data['time_to_complete'] ?? null;
        $conditioning->weight = $data['weight'] ?? null;
        $conditioning->date = $data['date'] ?? null;
        $conditioning->unit = $data['unit'] ?? null;
        $conditioning->amrap = $data['amrap'] ?? false;
        $conditioning->Pyramid = $data['Pyramid'] ?? false;
        $conditioning->intensity = $data['intensity'] ?? null;
        $conditioning->male = $data['male'] ?? null;
        $conditioning->female = $data['female'] ?? null;
        $conditioning->is_assigned = $data['is_assigned'] ?? false;
        //$conditioning->conditioning_id = $data['conditioning_id'] ?? null;

        $conditioning->save();

        return $conditioning;
    }
}
