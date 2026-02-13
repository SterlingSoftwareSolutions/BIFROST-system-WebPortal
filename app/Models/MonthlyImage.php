<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'month',
        'front_image',
        'side_image',
        'back_image',
        'user_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
