<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cat = DB::table('category_options')->where('category_name', 'Carry/Oly')->first();
        if ($cat) {
            // Rename the existing one to "Carry" (preserves relations for existing exercises)
            DB::table('category_options')->where('id', $cat->id)->update([
                'category_name' => 'Carry'
            ]);

            // Create "Oly" as a new category
            $exists = DB::table('category_options')->where('category_name', 'Oly')->exists();
            if (!$exists) {
                DB::table('category_options')->insert([
                    'category_name' => 'Oly',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $cat1 = DB::table('category_options')->where('category_name', 'Carry')->first();
        $cat2 = DB::table('category_options')->where('category_name', 'Oly')->first();

        if ($cat1 && $cat2) {
            DB::table('category_options')->where('id', $cat1->id)->update([
                'category_name' => 'Carry/Oly'
            ]);
            DB::table('category_options')->where('id', $cat2->id)->delete();
        }
    }
};
