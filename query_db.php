<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$rows = App\Models\DailyStrength::orderByDesc('id')->take(20)->get();
foreach($rows as $r) {
    echo $r->id . ' | class:' . $r->class_id . ' | type:' . $r->workout_format_type . ' | f_id:' . $r->workout_format_id . ' | is_completed:' . $r->is_completed . ' | date:' . $r->date . PHP_EOL;
}
