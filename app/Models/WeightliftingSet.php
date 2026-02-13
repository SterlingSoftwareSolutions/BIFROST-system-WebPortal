<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightliftingSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'sets',
        'reps',
        'weight',
        'alt_sets',
        'alt_reps',
        'weightlifting_id',
    ];

    public function weightlifting()
    {
        return $this->belongsTo(Weightlifting::class, 'weightlifting_id');
    }

        /**
     * Store a new WeightliftingSet record.
     *
     * @param array $data
     * @return WeightliftingSet
     */
    public static function store(array $data)
    {
        return self::create([
            'sets' => $data['sets'] ?? null,
            'reps' => $data['reps'] ?? null,
            'weight' => $data['weight'] ?? null,
            'alt_sets' => $data['alt_sets'] ?? null,
            'alt_reps' => $data['alt_reps'] ?? null,
            'weightlifting_id' => $data['weightlifting_id'] ?? null,
        ]);
    }
}
