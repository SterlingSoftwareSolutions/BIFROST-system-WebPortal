<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $table = 'tests';

    protected $fillable = [
        'category_id',
        'workout_id',
        'member_id',
        'workoutname',
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
    
    public function member()
    {
        return $this->belongsTo(Newprofile::class, 'member_id');
    }
    
    public static function store($data)
    {
        $test = new self();
        $test->category_id = $data['test-category'];
        $test->workout_id = $data['test-workout'];
        $test->member_id = $data['test-member'];
        $test->date = $data['date'];

        $test->save();
    }
}

