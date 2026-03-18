<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyWarmup extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'warmup_id',
        'workout_manager_id',
        'reps',
        'round_number',
        'date',
        'workout_format_type',
        'workout_format_id',
        'round_number',
        'exercise_time',
        'class_id',
    ];

    public function member()
    {
        return $this->belongsTo(Newprofile::class, 'member_id');
    }

    public function warmup()
    {
        return $this->belongsTo(Warmup::class, 'warmup_id');
    }

    public function workoutManager()
    {
        return $this->belongsTo(WorkoutManager::class, 'workout_manager_id');
    }

    /**
     * Get the workout format (polymorphic relation)
     * Can be: Round, Amrap, ForTime, Interval, Emom, Straight, Circuit, Pyramid
     */
    public function workoutFormat()
    {
        return $this->morphTo('workout_format', 'workout_format_type', 'workout_format_id');
    }

    public static function getDailyWarmupData($memberId, $date)
    {
        $data = self::with(['member', 'warmup.category'])
                    ->where('member_id', $memberId)
                    ->where('date', $date)
                    ->get();

        // Dump the data to see its structure

        return $data;
    }


}
