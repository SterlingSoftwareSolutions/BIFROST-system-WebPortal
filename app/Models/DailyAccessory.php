<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyAccessory extends Model
{
    use HasFactory;
    protected $table = 'daily_accessory';

    protected $fillable = [
        'member_id',
        'workout_manager_id',
        'workout_format_type',
        'workout_format_id',
        'set_number',
        'reps',
        'weight',
        'date',
    ];


    public function workoutFormat()
    {
        return $this->morphTo('workout_format', 'workout_format_type', 'workout_format_id');
    }
    public function workoutManager()
    {
        return $this->belongsTo(WorkoutManager::class, 'workout_manager_id');
    }
    
    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'accessory_id');
    }

    public static function getDailyaccessoryData($memberId, $date)
    {
        $data = self::with(['member', 'accessory.category'])
                    ->where('member_id', $memberId)
                    ->where('date', $date)
                    ->get();

        return $data;
    }
}
