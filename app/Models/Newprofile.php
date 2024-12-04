<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newprofile extends Model
{
    use HasFactory;
    protected $table = 'members';
    protected $fillable = [
        'firstname',
        'lastname',
        'dob',
        'gender',
        'age',
        'phone',
        'email',
        'address',
        'height',
        'weight',
        'bmr',
        'primary-goal',
        'subscription_level',
        'image_paths',
        'user_id'
    ];

    public function getProgressPhotosAttribute($value)
    {
        return json_decode($value, true);
    }

    // Mutator to encode the progress_photos attribute to JSON
    public function setProgressPhotosAttribute($value)
    {
        $this->attributes['progress_photos'] = json_encode($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'member_id');
    }
    public function dailyWarmups()
    {
        return $this->hasMany(DailyWarmup::class, 'warmup_id');
    }

    public function dailyStrenght()
    {
        return $this->hasMany(DailyStrength::class, 'strength_id');
    }
}
