<?php

namespace App\Models;

use App\Models\CategoryOption;
use App\Models\WeightliftingSet;
use App\Models\WorkoutLibrary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Weightlifting extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'workout_id',
        'workoutname',
        'weight',
        'restredwe',
        'restyellowwe',
        'restgreenwe',
        'altrestredwe',
        'altrestyellowwe',
        'altrestgreenwe',
        'intensity',
        'is_assigned',
        'alt_category_id',
        'alt_workout_id',
        'alt_workoutname',
        'alt_weight',
        'alt_intensity',
        'unit',
        'date',
    ];

    // Define relationship to category
    public function category()
    {
        return $this->belongsTo(CategoryOption::class, 'category_id');
    }

    // Define relationship to workout
    public function workout()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_id');
    }
    // Define the relationship with WeightliftingSet

    public function sets()
    {
        return $this->hasMany(WeightliftingSet::class, 'weightlifting_id');
    }
    // Define the relationship with the alternative Category
    public function altCategory()
    {
        return $this->belongsTo(CategoryOption::class, 'alt_category_id');
    }

    // Define the relationship with the alternative WorkoutLibrary
    public function altWorkout()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'alt_workout_id');
    }
    public function workouts()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_id');
    }
    public static function store($data)
    {
        // dd($data);
        Log::info("message for weightlifting data", $data);
        $weightlifting = new self();
        $weightlifting->category_id = $data['category'];
        $weightlifting->workout_id = $data['workout'];
        $weightlifting->workoutname = $data['name'] ?? null;
        $weightlifting->weight = $data['weigth'];
        $weightlifting->unit = $data['unit'];
        $weightlifting->restredwe = $data['restred'] ?? '00:00:00'; // Use default if not provided
        $weightlifting->restyellowwe = $data['restyellow'] ?? '00:00:00'; // Use default if not provided
        $weightlifting->restgreenwe = $data['restgreen'] ?? '00:00:00'; // Use default if not provided
        $weightlifting->intensity = $data['intensity'] ?? null;
        $weightlifting->alt_category_id = $data['alt-category'] ?? null;
        $weightlifting->alt_workout_id = $data['alt-workout'] ?? null;
        $weightlifting->alt_weight = $data['alt-weigth'] ?? null;
        $weightlifting->alt_workoutname = $data['alt-name'] ?? null;
        $weightlifting->altrestredwe = $data['alt-restred'] ?? '00:00:00'; // Use default if not provided
        $weightlifting->altrestyellowwe = $data['alt-restyellow'] ?? '00:00:00'; // Use default if not provided
        $weightlifting->altrestgreenwe = $data['alt-restgreen'] ?? '00:00:00'; // Use default if not provided
        $weightlifting->alt_intensity = $data['alt-intensity'] ?? null;
        $weightlifting->date =  $data['date'];

        // Save the model to the database
        $weightlifting->save();

        return $weightlifting;
    }
}
