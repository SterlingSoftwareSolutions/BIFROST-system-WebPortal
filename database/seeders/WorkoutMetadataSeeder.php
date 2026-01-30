<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Type;
use App\Models\Format;

use Illuminate\Support\Str;

class WorkoutMetadataSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Types
        $types = [
            'Warmup',
            'Strength',
            'Conditioning',
            'Weightlifting',
            'Accessory',
            "PR's"
        ];

        foreach ($types as $name) {
            Type::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            );
        }
        $this->command->info('Types seeded successfully.');

        // 2. Seed Formats
        $formats = [
            'Straight Sets',
            'Rounds',
            'AMRAP',
            'EMOM',
            'For Time',
            'Intervals',
            'Pyramid',
            'Circuit'
        ];

        foreach ($formats as $name) {
            Format::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            );
        }
        $this->command->info('Formats seeded successfully.');
    }
}
