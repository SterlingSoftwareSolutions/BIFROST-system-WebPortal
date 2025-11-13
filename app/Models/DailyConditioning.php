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
        'reps',
        'date',
        'weight',
    ];

    public function member()
    {
        return $this->belongsTo(Newprofile::class, 'member_id');
    }

    public function conditioning()
    {
        return $this->belongsTo(Conditioning::class, 'conditioning_id');
    }
}
