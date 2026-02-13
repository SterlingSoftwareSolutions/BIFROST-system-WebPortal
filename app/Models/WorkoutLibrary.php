<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutLibrary extends Model
{
    use HasFactory;
    protected $table = 'workout_libraries';
    protected $fillable = [
        'category_options_id',
        'type',
        'workout',
        'link',
    ];

    public function categoryOption()
    {
        return $this->belongsTo(CategoryOption::class, 'category_options_id');
    }
    // WorkoutLibrary.php (Model)
    public function clientManagements()
    {
        return $this->hasMany(ClientManagement::class, 'workout'); // Assuming 'workout_id' is the foreign key
    }

    public function warmups()
    {
        return $this->hasMany(Warmup::class, 'workout_id'); // Assuming 'workout_id' is the foreign key
    }
    public function strengths()
    {
        return $this->hasMany(Strength::class, 'workout_id'); // Assuming 'workout_id' is the foreign key
    }
    public function weightliftings()
    {
        return $this->hasMany(Weightlifting::class, 'workout_id'); // Assuming 'workout_id' is the foreign key
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'workout_id');
    }
    public function conditionings()
    {
        return $this->hasMany(Conditioning::class, 'workout_id');

    }
}
