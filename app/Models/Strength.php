<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strength extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'workout_id',
        'workoutname',
        'weight',
        'sets',
        'restred',
        'restyellow',
        'restgreen',
        'altrestred',
        'altrestyellow',
        'altrestgreen',
        'reps',
        'intensity',
        'alt_category_id',
        'alt_workout_id',
        'altweight',
        'altsets',
        'altreps',
        'altintensity',
        'date'
    ];

    public function category()
    {
        return $this->belongsTo(CategoryOption::class, 'category_id');
    }

    public function workout()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_id');
    }

    public function setstrengthsetsreps()
    {
        return $this->hasMany(StrengthSetRep::class, 'strength_id');
    }

    public function altCategory()
    {
        return $this->belongsTo(CategoryOption::class, 'alt_category_id');
    }

    public function altWorkout()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'alt_workout_id');
    }
    public static function store($data)
    {

        // dd($data);
        $strengthing = new self();
        $strengthing->category_id = $data['categorys'];
        $strengthing->workout_id = $data['workouts'];
        $strengthing->weight = $data['weigths'];
        $strengthing->workoutname = $data['names'] ?? null;
        $strengthing->restred = $data['restreds'] ?? '00:00:00';
        $strengthing->restyellow = $data['restyellows'] ?? '00:00:00';
        $strengthing->restgreen = $data['restgreens'] ?? '00:00:00'; // Use default if not provided
        $strengthing->intensity = $data['intensitys']?? null;
        $strengthing->alt_category_id = $data['alt-categorys'] ?? null;
        $strengthing->alt_workout_id = $data['alt-workouts'] ?? null;
        $strengthing->altweight = $data['alt-weigths'] ?? null;
        $strengthing->altrestred = $data['alt-restredwe'] ?? '00:00:00';
        $strengthing->altrestyellow = $data['alt-restyellows'] ?? '00:00:00';
        $strengthing->altrestgreen = $data['alt-restgreens'] ?? '00:00:00';// Use default if not provided
        $strengthing->altintensity = $data['alt-intensitys'] ?? null;
        $strengthing->date = $data['date'];

        // Save the model to the database
        $strengthing->save();

        return $strengthing;
    }


    public function sets()
    {
        return $this->hasMany(StrengthSetRep::class, 'strength_id');
    }
}
