<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStrength extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'strength_id',
        'reps',
        'date',
        'type',
        'weight',
    ];

    public function member()
    {
        return $this->belongsTo(Newprofile::class, 'member_id');
    }
    
    public function strenght()
    {
        return $this->belongsTo(Strength::class, 'strength_id');
    }

}
