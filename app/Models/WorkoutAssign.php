<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutAssign extends Model
{
    use HasFactory;
    protected $table = 'workout_assign';
    protected $fillable = ['class_id', 'workout_id', 'workout_type', 'date'];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function workout()
    {
        switch ($this->workout_type) {
            case 'strength':
                return $this->belongsTo(Strength::class, 'workout_id');
            case 'weightlifting':
                return $this->belongsTo(Weightlifting::class, 'workout_id');
            case 'conditioning':
                return $this->belongsTo(Conditioning::class, 'workout_id');
            case 'warmup':
                return $this->belongsTo(Warmup::class, 'workout_id');
            case 'test':
                return $this->belongsTo(Test::class, 'workout_id');
            default:
                return null;
        }
    }
}
