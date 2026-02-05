<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutManager extends Model
{
    use HasFactory;

    protected $table = 'workout_manager';

    protected $fillable = [
        'workout_name',
        'type_id',
        'format_id',
        'number',
        'status',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function format()
    {
        return $this->belongsTo(Format::class);
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function intervals()
    {
        return $this->hasMany(Interval::class);
    }

    public function straights()
    {
        return $this->hasMany(Straight::class);
    }

    public function amraps()
    {
        return $this->hasMany(Amrap::class);
    }

    public function emoms()
    {
        return $this->hasMany(Emom::class);
    }

    public function circuits()
    {
        return $this->hasMany(Circuit::class);
    }

    public function pyramids()
    {
        return $this->hasMany(Pyramid::class);
    }

    public function forTimes()
    {
        return $this->hasMany(ForTime::class);
    }
}
