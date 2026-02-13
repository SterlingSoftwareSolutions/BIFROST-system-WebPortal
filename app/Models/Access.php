<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{

    use HasFactory;
    // protected $table = 'access';
    protected $fillable = [
        'dashboard',
        'access',
        'client_management',
        'workout_library',
        'session',
        'financial',
        'communication',
        'statistics',
        'user_dashboard',
        'profile',
        'goals',
        'achievements',
        'settings',
        'access_type',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
