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

        // Create and save a new WeightliftingSet record
        return self::create([
            'sets' => 1,
            'reps' => $data[1] ?? null,
            'alt_sets' => $data[2] ?? null,
            'alt_reps' => $data[3] ?? null,
            'weightlifting_id' => $data[4] ?? null,
        ]);
    }
}
