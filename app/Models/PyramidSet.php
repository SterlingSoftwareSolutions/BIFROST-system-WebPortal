<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PyramidSet extends Model
{
    use HasFactory;
    protected $table = 'pyramidset';
    protected $fillable = [
        'sets',
        'reps',
        'unit',
        'pyramidweight',
        'pyramidmale',
        'pyramidfemale',
        'conditioning_id',
    ];

    public static function store(array $data)
    {
        // Optionally inspect the incoming data
        // dd($data);

        return self::create([
            'sets' => $data['sets'] ?? null,
            'reps' => $data['reps'] ?? null,
            'unit' => $data['unit'] ?? null,
            'pyramidweight' => $data['pyramidweight'] ?? null,
            'pyramidmale' => $data['pyramidmale'] ?? null,
            'pyramidfemale' => $data['pyramidfemale'] ?? null,
            'conditioning_id' => $data['conditioning_id'] ?? null,
        ]);
    }
}
