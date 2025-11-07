<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyWeightlifting extends Model
{
    use HasFactory;
    protected $fillable = [
        'member_id',
        'weightlifting_id',
        'reps',
        'date',
        'weight',
    ];

    public function member()
    {
        return $this->belongsTo(Newprofile::class, 'member_id');
    }

    public function weightlifting()
    {
        return $this->belongsTo(Weightlifting::class, 'weightlifting_id');
    }
}
