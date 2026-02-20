<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyConditioning extends Model
{
    use HasFactory;
    protected $fillable = [
        'member_id',
        'conditioning_id',
        'workout_manager_id',
        'reps',
        'date',
        'weight',
        'workout_format_type',
        'workout_format_id',
    ];

    public function member()
    {
        return $this->belongsTo(Newprofile::class, 'member_id');
    }

    public function conditioning()
    {
        return $this->belongsTo(Conditioning::class, 'conditioning_id');
    }
    public function workoutFormat()
    {
        return $this->morphTo('workout_format', 'workout_format_type', 'workout_format_id');
    }

    public static function getDailyConditioningData($memberId, $date)
    {
        $data = self::with(['member', 'conditioning.category'])
                    ->where('member_id', $memberId)
                    ->where('date', $date)
                    ->get();
        
        return $data;
    }
}
