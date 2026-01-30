<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StraightSet extends Model
{
    use HasFactory;

    protected $table = 'straight_sets';

    protected $fillable = [
        'straight_id',
        'workout_libraries_id',
        'res',        // reps
        'trainload',  // load
        'unittype',   // unit
    ];

    public function parent()
    {
        return $this->belongsTo(Straight::class, 'straight_id');
    }

    public function workoutLibrary()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_libraries_id');
    }
}
