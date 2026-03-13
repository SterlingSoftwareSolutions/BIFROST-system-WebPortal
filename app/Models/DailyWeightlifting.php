<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyWeightlifting extends Model
{
    use HasFactory;
    protected $table = 'daily_weightlifting';
    protected $fillable = [
        'member_id',
        'weightlifting_id',
        'workout_manager_id',
        'reps',
        'date',
        'weight',
        'set_number',
        'workout_format_type',
        'workout_format_id',
        'round_number',
    ];

    public function member()
    {
        return $this->belongsTo(Newprofile::class, 'member_id');
    }

    public function weightlifting()
    {
        return $this->belongsTo(Weightlifting::class, 'weightlifting_id');
    }

     public function workoutManager()
    {
        return $this->belongsTo(WorkoutManager::class, 'workout_manager_id');
    }

    public function workoutFormat()
    {
        return $this->morphTo('workout_format', 'workout_format_type', 'workout_format_id');
    }

    public static function getDailyweightliftingData($memberId, $date)
    {
        $data = self::with(['member', 'weightlifting.category'])
                    ->where('member_id', $memberId)
                    ->where('date', $date)
                    ->get();

        return $data;
    }

}
