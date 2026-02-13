<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSession extends Model
{
    use HasFactory;
    protected $table = 'reservation';

    protected $fillable = [
        'user_id', 'classes_id', 'is_reserved'
    ];
}
