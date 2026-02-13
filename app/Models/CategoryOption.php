<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryOption extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_name',
    ];
    
    public function workout()
    {
        return $this->hasMany(workoutLibrary::class);
        
    }
    public function workoutLibrary()
    {
        return $this->belongsTo(WorkoutLibrary::class, 'workout_id');
    }
    public function warmups()
    {
        return $this->hasMany(Warmup::class, 'category_id');
    }

    public function strengths()
    {
        return $this->hasMany(Strength::class, 'category_id');
    }

    public function conditioning()
    {
        return $this->hasMany(Conditioning::class, 'category_id');
    }
    
}
