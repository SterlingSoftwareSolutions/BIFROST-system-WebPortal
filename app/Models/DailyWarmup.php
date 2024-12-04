<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyWarmup extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'warmup_id',
        'reps',
        'date',
    ];

    public function member()
    {
        return $this->belongsTo(Newprofile::class, 'member_id');
    }

    public function warmup()
    {
        return $this->belongsTo(Warmup::class, 'warmup_id');
    }
    public static function getDailyWarmupData($memberId, $date)
    {
        $data = self::with(['member', 'warmup.category'])
                    ->where('member_id', $memberId)
                    ->where('date', $date)
                    ->get();
        
        // Dump the data to see its structure
        
        return $data;
    }
    
}