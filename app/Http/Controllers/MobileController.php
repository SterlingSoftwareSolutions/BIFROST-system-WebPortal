<?php

namespace App\Http\Controllers;

use App\Models\CategoryOption;
use App\Models\Classes;
use App\Models\ClientManagement;
use App\Models\Conditioning;
use App\Models\DailyConditioning;
use App\Models\DailyStrength;
use App\Models\Test;
use App\Models\DailyWarmup;
use App\Models\DailyWeightlifting;
use App\Models\Newprofile;
use App\Models\ReservationSession;
use Exception;
use Carbon\Carbon;
use App\Models\Score;
use App\Models\Strength;
use App\Models\UserScore;
use App\Models\Warmup;
use App\Models\Weightlifting;
use App\Models\WorkoutLibrary;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\WorkoutAssign;
use App\Models\WorkoutManager;
use App\Models\Round;
use App\Models\Amrap;
use App\Models\ForTime;
use App\Models\Interval;
use App\Models\Emom;
use App\Models\Straight;
use App\Models\Circuit;
use App\Models\Pyramid;

class MobileController extends Controller
{
    //
    public function login()
    {
        return view("mobile.auth.login");
    }
    public function forgetpin()
    {
        return view("mobile.auth.forgetpin");
    }

    // Training Day view
    public function trainingday(Request $request)
    {
        // Check if 'selected_day' is null and store today's date if it is
        // if (!Session::has('selected_day')) {
        //     session(['selected_day' => Carbon::now()->format('d/m/Y')]);
        // }

        // $dates = [];
        // $startOfWeek = Carbon::now()->startOfWeek(); // Get Monday

        // for ($i = 0; $i < 7; $i++) {
        //     $dates[] = $startOfWeek->copy()->addDays($i)->format('d/m/Y');
        // }

        // // Get the selected day from the session
        // $selectedDay = session('selected_day');

        // return view("mobile.user.trainingday", compact('dates', 'selectedDay'));

        $dates = [];
        $today = Carbon::now()->startOfDay();

        for ($i = 0; $i < 7; $i++) {
            $dates[] = $today->copy()->addDays($i);
        }

        // Get 6AM classes for each date
        $classesByDate = [];
        foreach ($dates as $date) {
            //$date = Carbon::createFromFormat('d/m/Y', $storedDay);
            $dayName = $date->format('l'); // Get the full day name (e.g., Monday)
            $formattedDate = $date->format('d/m/Y'); // Format the date

            // Combine day name and date
            $dayWithDate = $date->format('d/m/y') . ' ' . $dayName;
            //$formattedDate = $date->format('d/m/Y');
            //dd($formattedDate);
            $class = Classes::where('date', $dayWithDate)
                ->where('time', '06:00:00')
                ->first(); // use `first()` if you're expecting a single class

            $classesByDate[$formattedDate] = $class;
            //dd($classesByDate);
        }

        return view('mobile.user.trainingday', [
            'dates' => $dates, // still Carbon objects — good
            'classesByDate' => $classesByDate,
            'defaultTime' => '06:00:00'
        ]);
    }

    public function getClassSlots(Request $request)
    {
        $time = $request->query('time');
        $today = Carbon::now()->startOfDay();
        $slots = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->addDays($i);

            $class = Classes::where('date', $date->format('d/m/y l'))
                ->where('time', $time)
                ->first();

            $userReservation = null;
            if ($class) {
                $userReservation = \App\Models\ReservationSession::where('user_id', auth()->id())
                    ->where('classes_id', $class->id)
                    ->exists();
            }

            $slots[] = [
                'id' => $class?->id,
                'date' => $date->format('d/m/Y'),
                'day_name' => $date->format('l'),
                'is_today' => $date->isToday(),
                'class' => $class ? [
                    'time' => \Carbon\Carbon::parse($class->time)->format('g:i A'),
                    'duration' => $class->duration,
                    'spots' => $class->spots,
                ] : null,
                'reserved' => $userReservation,
            ];
        }

        return response()->json($slots);
    }

    public function reserve(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $class = Classes::find($request->class_id);

        // Check if spots are available
        if ($class->availablespots <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No spots available for this class.'
            ], 400);
        }

        // Check if user already reserved this class
        $existing = ReservationSession::where('user_id', Auth::id())
            ->where('classes_id', $class->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reserved this class.'
            ], 400);
        }

        // Create reservation
        $reservation = ReservationSession::create([
            'user_id' => Auth::id(),
            'classes_id' => $class->id,
            'is_reserved' => true
        ]);

        // Decrease spot count
        $class->availablespots = $class->availablespots - 1;
        $class->save();


        return response()->json([
            'success' => true,
            'message' => 'Reservation successful!',
            'reservation' => [
                'id' => $reservation->id,
                'class_id' => $reservation->classes_id,
                'user_id' => $reservation->user_id,
                'is_reserved' => $reservation->is_reserved
            ],
            'remainingSpots' => $class->availablespots
        ], 200);
    }


    public function cancel(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $reservation = ReservationSession::where('user_id', Auth::id())
            ->where('classes_id', $request->class_id)
            ->first();

        if ($reservation) {
            $reservation->delete();

            // Increase the spot count
            $class = Classes::find($request->class_id);
            $class->increment('availablespots');

            return response()->json([
                'success' => true,
                'message' => 'Reservation cancelled successfully.',
                'remainingSpots' => $class->availablespots
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No reservation found.'
        ], 400);
    }

    // Training Day select date
    public function selectday(Request $request)
    {
        // Retrieve the day from the request
        $day = $request->day;

        // Store the day in the session
        session(['selected_day' => $day]);

        // Retrieve the day from the session
        $storedDay = session('selected_day');

        // Return the day from the session as a JSON response
        return response()->json(['day' => $storedDay]);
    }

    // Readinessscore page view
    public function readinessscore()
    {
        // Retrieve the day from the session
        $storedDay = session('selected_day');

        if (!$storedDay) {
            $storedDay = Carbon::now()->format('d/m/Y');
            session(['selected_day' => $storedDay]);
        }

        // Format the stored day to the desired format
        $date = Carbon::createFromFormat('d/m/Y', $storedDay);
        $dayName = $date->format('l'); // Get the full day name (e.g., Monday)
        $formattedDate = $date->format('d/m/Y'); // Format the date

        // Combine day name and date
        $dayWithDate = $dayName . ' ' . $formattedDate;
        $user = Auth::user()->id;

        $userscore = UserScore::where('user_id', $user)->where('selected_day', $dayWithDate)->first();
        // dd($userscore);

        return view("mobile.user.readinessscore", compact('dayWithDate', 'userscore'));
    }

    // store Score
    public function storescore(Request $request)
    {
        $validatedData = $request->validate([
            'selected_day' => 'required|string',
            'sleep_input' => 'required|string',
            'alertness_input' => 'required|string',
            'excitement_input' => 'required|string',
            'stress_input' => 'required|string',
            'soreness_input' => 'required|string',
            'score' => 'required|integer',
        ]);

        $user = $request->user();

        // Find an existing score for the same user and date, or create a new one
        $score = $user->scores()->updateOrCreate(
            ['user_id' => $user->id, 'selected_day' => $validatedData['selected_day']],
            $validatedData
        );

        return redirect()->route('mobile.workout')->with('success', 'Score saved successfully.');
    }


    public function workout()
    {
        $storedDay = session('selected_day');

        if (!$storedDay) {
            $storedDay = Carbon::now()->format('d/m/Y');
            session(['selected_day' => $storedDay]);
        }

        // Format the stored day to the desired format
        $date = Carbon::createFromFormat('d/m/Y', $storedDay);
        $dayName = $date->format('l'); // Get the full day name (e.g., Monday)
        $formattedDate = $date->format('d/m/y'); // Format the date

        // Combine day name and date
        $dayWithDate = $formattedDate . ' ' . $dayName;

        //get warmup details for specific date
        $tabwarmup = 'warmup';
        $date = $dayWithDate;
        $detailswarmup = Warmup::where('date', $dayWithDate)
            ->where('is_assigned', 1)
            ->with('workouts')
            ->with('workouts.categoryOption')
            ->get();

        // //get strength details for specific date
        $tabstrength = 'strength';
        $date = $dayWithDate;
        $detailsstrength = Strength::where('date', $date)
            ->where('is_assigned', 1)
            ->with('sets')
            ->with('sets.strengthing')
            ->with('workout')
            ->with('workout.categoryOption')
            ->get();

        //get conditioning details for specific date
        $tabconditioning = 'conditioning';
        $date = $dayWithDate;
        $detailsconditioning = Conditioning::where('date', $date)
            ->where('is_assigned', 1)
            ->with('workout')
            ->with('workout.categoryOption')
            ->get();

        //  //get warup details for specific date
        $tabweightweight = 'weightlifting';
        $date = $dayWithDate;
        $detailsweight = Weightlifting::where('date', $date)
            ->where('is_assigned', 1)
            ->with('sets')
            ->with('sets.weightlifting')
            ->with('workouts')
            ->with('workouts.categoryOption')
            ->get();

        //get warup details for specific date
        $tabconditioning = 'conditioning';
        $date = $dayWithDate;
        $detailsconditioning = Conditioning::where('date', $date)
            ->with('workout')
            ->get();

        $tabtest = 'Test';
        $date = $dayWithDate;
        $detailstest = Test::where('date', $date)
            ->with('workout')
            ->with('workouts.categoryOption')
            ->with('member')
            ->get();


        //  foreach ($detailsweight as $weightlifting) {
        //     foreach ($weightlifting->sets as $set) {
        //         dd($set->sets, $set->reps); // Dump and display the values of sets and reps
        //     }
        // }

        //dd($detailsstrength);

        return view('mobile.user.workout', compact('dayWithDate', 'detailswarmup', 'detailsstrength', 'detailsconditioning', 'detailsweight', 'detailstest','detailsconditioning'));
    }

    //store daily warmup workout after clicking
    public function storewarmupdaily(Request $request)
{
    try {
        Log::info('storewarmupdaily function called.');

        $userId = Auth::id();
        $memberId = Newprofile::where('user_id', $userId)->value('id');
        Log::info('Authenticated user ID: ' . $userId . ', Member ID: ' . $memberId);

        // Get warmup array directly
        $warmupItems = $request->all();
        Log::info('Received warmup payload:', ['warmupItems' => $warmupItems]);

        $responses = [];

            foreach ($warmupItems as $item) {
                $validator = Validator::make($item, [
                    'workout_manager_id' => 'required|integer',
                    'workout_format_type' => 'required|string|in:rounds,amrap,for-time,intervals,emom,straight-sets,circuit,pyramid',
                    'workout_format_id' => 'required|integer',
                    'reps' => 'required|integer',
                    'round_number' => 'nullable|string',
                    'date' => 'required|string',
                    'exercise_time' => 'nullable|string',
                    'class_Id' => 'required|integer',
                    'notes' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $responses[] = [
                        'workout_manager_id' => $item['workout_manager_id'] ?? null,
                        'workout_format_id' => $item['workout_format_id'] ?? null,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ];
                    continue;
                }

                $validatedData = $validator->validated();
                $storedDay = $validatedData['date'];
                $exerciseTime = $validatedData['exercise_time'] ?? null;
                $roundNumber = $validatedData['round_number'] ?? null;
                $workoutFormatType = $validatedData['workout_format_type'];
                $workoutFormatId = $validatedData['workout_format_id'];
                $classId = $validatedData['class_Id'];

                $roundNumber = $item['round_number'] ?? null; // only get if exists

                // Find existing record
                $query = DailyWarmup::where('member_id', $memberId)
                    ->where('workout_manager_id', $validatedData['workout_manager_id'])
                    ->where('workout_format_type', $workoutFormatType)
                    ->where('workout_format_id', $workoutFormatId)
                    ->where('class_id', $classId)
                    ->where('date', $storedDay);

                if ($workoutFormatType === 'amrap' && $roundNumber !== null) {
                    $query->where('round_number', $roundNumber);
                }

                $dailyWarmup = $query->first();

                Log::info('Existing warmup record:', ['dailyWarmup' => $dailyWarmup?->toArray()]);

            if ($dailyWarmup) {
                $dailyWarmup->update([
                    'reps' => $validatedData['reps'],
                    'date' => $storedDay,
                    'workout_format_type' => $workoutFormatType,
                    'workout_format_id' => $workoutFormatId,
                    'round_number' => $roundNumber, // save only if exists
                    'exercise_time' => $exerciseTime,
                    'class_id' => $classId,
                    'notes' => $validatedData['notes'] ?? null,
                ]);
                $message = 'Warm-up updated successfully';
                Log::info('Warm-up updated', [
                    'workout_manager_id' => $validatedData['workout_manager_id'],
                    'workout_format_type' => $workoutFormatType,
                    'workout_format_id' => $workoutFormatId,
                    'round_number' => $roundNumber,
                ]);
            } else {
                $dailyWarmup = DailyWarmup::create([
                    'member_id' => $memberId,
                    'reps' => $validatedData['reps'],
                    'date' => $storedDay,
                    'workout_manager_id' => $validatedData['workout_manager_id'],
                    'workout_format_type' => $workoutFormatType,
                    'workout_format_id' => $workoutFormatId,
                    'round_number' => $roundNumber, // save only if exists
                    'exercise_time' => $exerciseTime,
                    'class_id' => $classId,
                    'notes' => $validatedData['notes'] ?? null,
                ]);
                $message = 'Warm-up saved successfully';
                Log::info('New warm-up created', [
                    'workout_manager_id' => $validatedData['workout_manager_id'],
                    'workout_format_type' => $workoutFormatType,
                    'workout_format_id' => $workoutFormatId,
                    'round_number' => $roundNumber,
                ]);
            }

            $responses[] = [
                'workout_manager_id' => $validatedData['workout_manager_id'],
                'workout_format_type' => $workoutFormatType,
                'workout_format_id' => $workoutFormatId,
                'round_number' => $roundNumber, // include if exists
                'message' => $message,
                'daily_warmup_id' => $dailyWarmup->id,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Warm-ups processed successfully',
            'results' => $responses,
        ]);

    } catch (\Exception $e) {
        Log::error('Error saving warm-up: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'An error occurred while saving the warm-ups',
            'details' => $e->getMessage(),
        ], 500);
    }
}

    public function storestrengthdaily(Request $request)
    {
        try {
            Log::info('storestrengthdaily function called.');

            $userId = Auth::id();
            $memberId = Newprofile::where('user_id', $userId)->value('id');
            Log::info('Authenticated user ID: ' . $userId . ', Member ID: ' . $memberId);

            if (!$memberId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            $strengthItems = $request->all();
            Log::info('Received strength payload:', ['strengthItems' => $strengthItems]);

            if (!is_array($strengthItems) || count($strengthItems) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No strength data received.',
                ]);
            }

            $responses = [];

            foreach ($strengthItems as $item) {
                $validator = Validator::make($item, [
                    'workout_manager_id' => 'required|integer',
                    'workout_format_type' => 'required|string',
                    'workout_format_id' => 'required|integer',
                    'reps' => 'required|integer',
                    'set_number' => 'nullable|integer',
                    'round_number' => 'nullable|string',
                    'weight' => 'nullable|numeric',
                    'date' => 'required|string', // only required for AMRAP, but we will check conditionally in code
                    'exercise_time' => 'nullable|string',
                    'class_Id' => 'required|integer',
                    'notes' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $responses[] = [
                        'workout_manager_id' => $item['workout_manager_id'] ?? null,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ];
                    continue;
                }

                $validatedData = $validator->validated();
                $storedDay = $validatedData['date'];
                $excerciseTime = $validatedData['exercise_time'] ?? null;
                $weight = $validatedData['weight'] ?? $validatedData['training_load'] ?? null;
                $setNumber = $validatedData['set_number'] ?? null;
                $roundNumber = $validatedData['round_number'] ?? null;
                $workoutFormatType = $validatedData['workout_format_type'];
                $workoutFormatId = $validatedData['workout_format_id'];
                $workoutManagerId = $validatedData['workout_manager_id'];
                $roundNumber = $validatedData['round_number'] ?? null;
                $classId = $validatedData['class_Id'];

                // Check if record exists for this member + workout_manager_id + format checks + set_number
                $query = DailyStrength::where('member_id', $memberId)
                    ->where('workout_manager_id', $workoutManagerId)
                    ->where('workout_format_type', $workoutFormatType)
                    ->where('workout_format_id', $workoutFormatId)
                    ->where('date', $storedDay)
                    ->where('class_id', $classId)
                    ->where('set_number', $setNumber);

                    /**
                     * If AMRAP format → also check round_number
                     */
                    if ($workoutFormatType === 'amrap' && $roundNumber !== null) {
                        $query->where('round_number', $roundNumber);
                    }

                $dailyStrength = $query->first();

                Log::info('Existing strength record:', ['dailyStrength' => $dailyStrength?->toArray()]);

                if ($dailyStrength) {
                    // Update existing record
                    $dailyStrength->update([
                        'reps' => $validatedData['reps'],
                        'weight' => $weight,
                        'date' => $storedDay,
                        'round_number' => $roundNumber,
                        'class_id' => $classId,
                        'notes' => $validatedData['notes'] ?? null,
                    ]);
                    $message = 'Strength updated successfully';
                    Log::info('Strength updated', [
                        'workout_manager_id' => $workoutManagerId,
                        'set_number' => $setNumber
                    ]);
                } else {
                    // Create new record
                    $dailyStrength = DailyStrength::create([
                        'member_id' => $memberId,
                        'workout_manager_id' => $workoutManagerId,
                        'workout_format_type' => $workoutFormatType,
                        'workout_format_id' => $workoutFormatId,
                        'reps' => $validatedData['reps'],
                        'weight' => $weight,
                        'set_number' => $setNumber,
                        'round_number' => $roundNumber,
                        'exercise_time' => $excerciseTime,
                        'date' => $storedDay,
                        'class_id' => $classId,
                        'notes' => $validatedData['notes'] ?? null,
                    ]);
                    $message = 'Strength saved successfully';
                    Log::info('New strength record created', [
                        'workout_manager_id' => $workoutManagerId,
                        'set_number' => $setNumber
                    ]);
                }

                $responses[] = [
                    'workout_manager_id' => $workoutManagerId,
                    'set_number' => $setNumber,
                    'round_number' => $roundNumber,
                    'message' => $message,
                    'daily_strength_id' => $dailyStrength->id,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Strength workouts processed successfully',
                'results' => $responses,
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving strength workout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while saving the strength workouts',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


    public function storeweightliftingdaily(Request $request)
    {      log::info('storeweightliftingdaily function called.');
        try {
            Log::info('storeweightliftingdaily function called.');

            $userId = Auth::id();
            $memberId = Newprofile::where('user_id', $userId)->value('id');
            Log::info('Authenticated user ID: ' . $userId . ', Member ID: ' . $memberId);

            if (!$memberId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            // Get weightlifting array directly
            $weightliftingItems = $request->all();
            Log::info('Received weightlifting payload:', ['weightliftingItems' => $weightliftingItems]);

            if (!is_array($weightliftingItems) || count($weightliftingItems) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No weightlifting data received.',
                ]);
            }

            $responses = [];

            foreach ($weightliftingItems as $item) {
                $validator = Validator::make($item, [
                    'workout_manager_id' => 'required|integer',
                    'workout_format_type' => 'required|string',
                    'workout_format_id' => 'required|integer',
                    'reps' => 'required|integer',
                    'weight' => 'nullable|numeric',
                    'set_number' => 'nullable|integer',
                    'round_number' => 'nullable|string',
                    'date' => 'required|string',
                    'round_number' => 'nullable|string', // only required for AMRAP, but we will check conditionally in code
                  'exercise_time' => 'nullable|string',
                  'class_Id' => 'required|integer',
                  'notes' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $responses[] = [
                        'workout_manager_id' => $item['workout_manager_id'] ?? null,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ];
                    continue;
                }

                $validatedData = $validator->validated();
                $storedDay = $validatedData['date'];
                $weight = $validatedData['weight'] ?? null;
                $setNumber = $validatedData['set_number']??null;
                $roundNumber = $validatedData['round_number'] ?? null;
                $exerciseTime = $validatedData['exercise_time'] ?? null;
                $workoutFormatType = $validatedData['workout_format_type'];
                $workoutFormatId = $validatedData['workout_format_id'];
                $workoutManagerId = $validatedData['workout_manager_id'];
                $roundNumber = $validatedData['round_number'] ?? null;
                $classId = $validatedData['class_Id'] ?? null;


                // Check if record exists for this member + workout_manager_id + format checks + set_number
                $query = DailyWeightlifting::where('member_id', $memberId)
                    ->where('workout_manager_id', $workoutManagerId)
                    ->where('workout_format_type', $workoutFormatType)
                    ->where('workout_format_id', $workoutFormatId)
                    ->where('date', $storedDay)
                    ->where('class_id', $classId)
                    ->where('set_number', $setNumber);

                    /**
                 * If AMRAP format → also check round_number
                 */
                if ($workoutFormatType === 'amrap' && $roundNumber !== null) {
                    $query->where('round_number', $roundNumber);
                }

                $dailyWeightlifting = $query->first();

                Log::info('Existing weightlifting record:', ['dailyWeightlifting' => $dailyWeightlifting?->toArray()]);

                if ($dailyWeightlifting) {
                    // Update existing record
                    $dailyWeightlifting->update([
                        'reps' => $validatedData['reps'],
                        'weight' => $weight,
                        'date' => $storedDay,
                        'round_number' => $roundNumber,
                        'exercise_time' => $exerciseTime,
                        'class_id' => $classId,
                        'notes' => $validatedData['notes'] ?? null,
                    ]);
                    $message = 'Weightlifting updated successfully';
                    Log::info('Weightlifting updated', [
                        'workout_manager_id' => $workoutManagerId,
                        'set_number' => $setNumber
                    ]);
                } else {
                    // Create new record
                    $dailyWeightlifting = DailyWeightlifting::create([
                        'member_id' => $memberId,
                        'workout_manager_id' => $workoutManagerId,
                        'workout_format_type' => $workoutFormatType,
                        'workout_format_id' => $workoutFormatId,
                        'reps' => $validatedData['reps'],
                        'weight' => $weight,
                        'set_number' => $setNumber,
                        'round_number' => $roundNumber,
                        'date' => $storedDay,
                        'exercise_time' => $exerciseTime,
                        'class_id' => $classId,
                        'notes' => $validatedData['notes'] ?? null,
                    ]);
                    $message = 'Weightlifting saved successfully';
                    Log::info('New weightlifting record created', [
                        'workout_manager_id' => $workoutManagerId,
                        'set_number' => $setNumber
                    ]);
                }

                $responses[] = [
                    'workout_manager_id' => $workoutManagerId,
                    'set_number' => $setNumber,
                    'round_number' => $roundNumber,
                    'message' => $message,
                    'daily_weightlifting_id' => $dailyWeightlifting->id,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Weightlifting workouts processed successfully',
                'results' => $responses,
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving weightlifting workout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while saving the weightlifting workouts',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeconditioningdaily(Request $request)
    {
        try {
            Log::info('storeconditioningdaily function called.');

            $userId = Auth::id();
            $memberId = Newprofile::where('user_id', $userId)->value('id');
            Log::info('Authenticated user ID: ' . $userId . ', Member ID: ' . $memberId);

            if (!$memberId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            // Get conditioning array directly
            $conditioningItems = $request->all();
            Log::info('Received conditioning payload:', ['conditioningItems' => $conditioningItems]);

            if (!is_array($conditioningItems) || count($conditioningItems) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No conditioning data received.',
                ]);
            }

            $responses = [];

            foreach ($conditioningItems as $item) {
                $validator = Validator::make($item, [
                    'workout_manager_id' => 'required|integer',
                    'workout_format_type' => 'required|string',
                    'workout_format_id' => 'required|integer',
                    'date' => 'required|string',
                    'round_number' => 'nullable|string', // only required for AMRAP, but we will check conditionally in code
                   # 'conditioning_id' => 'required|integer|exists:conditionings,id',
                    'reps' => 'nullable|integer',
                    'class_Id' => 'required|integer',
                   # 'weight' => 'nullable|numeric',
                    'exercise_time' => 'nullable|string',
                    'notes' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $responses[] = [
                        'workout_manager_id' => $item['workout_manager_id'] ?? null,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ];
                    continue;
                }

                $validatedData = $validator->validated();
                $storedDay = $validatedData['date'];
                $weight = $validatedData['weight'] ?? null;
                $reps = $validatedData['reps'] ?? null;
                $roundNumber = $validatedData['round_number'] ?? null;
                $exerciseTime = $validatedData['exercise_time'] ?? null;
                $workoutFormatType = $validatedData['workout_format_type'];
                $workoutFormatId = $validatedData['workout_format_id'];
                $workoutManagerId = $validatedData['workout_manager_id'];
                $classId = $validatedData['class_Id'];

                //Check if record exists for this member + workout_manager_id + format checks + date
                $query = DailyConditioning::where('member_id', $memberId)
                    ->where('workout_manager_id', $workoutManagerId)
                    ->where('workout_format_type', $workoutFormatType)
                    ->where('workout_format_id', $workoutFormatId)
                    ->where('class_id', $classId)
                    ->where('date', $storedDay);

                /**
                 * If AMRAP format → also check round_number so each round is separate.
                 * EMOM intentionally does NOT filter by round_number here — we want
                 * a single row per exercise that gets UPDATED on every round save,
                 * with round_number reflecting the latest completed round (e.g. "3/3").
                 */
                if ($workoutFormatType === 'amrap' && $roundNumber !== null) {
                    $query->where('round_number', $roundNumber);
                }

                $dailyConditioning = $query->first();

                Log::info('Existing conditioning record:', ['dailyConditioning' => $dailyConditioning?->toArray()]);

                if ($dailyConditioning) {
                    // Update existing record
                    $dailyConditioning->update([
                        'reps' => $reps,
                        'weight' => $weight,
                        'date' => $storedDay,
                        'round_number' => $roundNumber,
                        //'conditioning_id' => $conditioningId,
                        'class_id' => $classId,
                      'exercise_time' => $exerciseTime,
                      'notes' => $validatedData['notes'] ?? null,
                    ]);
                    $message = 'Conditioning updated successfully';
                    Log::info('Conditioning updated', ['workout_manager_id' => $workoutManagerId]);
                } else {
                    // Create new record
                    $dailyConditioning = DailyConditioning::create([
                        'member_id' => $memberId,
                        'workout_manager_id' => $workoutManagerId,
                        'workout_format_type' => $workoutFormatType,
                        'workout_format_id' => $workoutFormatId,
                        //'conditioning_id' => $conditioningId,
                        'reps' => $reps,
                        'weight' => $weight,
                        'round_number' => $roundNumber,
                        'date' => $storedDay,
                        'exercise_time' => $exerciseTime,
                        'class_id' => $classId,
                        'notes' => $validatedData['notes'] ?? null,
                    ]);
                    $message = 'Conditioning saved successfully';
                    Log::info('New conditioning record created', ['workout_manager_id' => $workoutManagerId]);
                }

                $responses[] = [
                    'workout_manager_id' => $workoutManagerId,
                    'round_number' => $roundNumber,
                    'message' => $message,
                    'daily_conditioning_id' => $dailyConditioning->id,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Conditioning workouts processed successfully',
                'results' => $responses,
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving conditioning workout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while saving the conditioning workouts',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeaccessorydaily(Request $request)
    {
        try {

            $userId = Auth::id();
            $memberId = Newprofile::where('user_id', $userId)->value('id');
            Log::info('Authenticated user ID: ' . $userId . ', Member ID: ' . $memberId);

            if (!$memberId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            $accessoryItems = $request->all();
            Log::info('Received accessory payload:', ['accessoryItems' => $accessoryItems]);

            if (!is_array($accessoryItems) || count($accessoryItems) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No accessory data received.',
                ]);
            }

            $responses = [];

            foreach ($accessoryItems as $item) {
                $validator = Validator::make($item, [
                    'workout_manager_id' => 'required|integer',
                    'workout_format_type' => 'required|string',
                    'workout_format_id' => 'required|integer',
                    'reps' => 'nullable|integer',
                    'weight' => 'nullable|numeric',
                    'set_number' => 'nullable|integer',
                    'round_number' => 'nullable|string',
                    'date' => 'required|string',
                    'class_Id' => 'required|integer',
                    'exercise_time' => 'nullable|string',
                    'notes' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $responses[] = [
                        'workout_manager_id' => $item['workout_manager_id'] ?? null,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ];
                    continue;
                }

                $validatedData = $validator->validated();
                $storedDay = $validatedData['date'];
                $weight = $validatedData['weight'] ?? null;
                $reps = $validatedData['reps'] ?? 0;
                $setNumber = $validatedData['set_number'] ?? null;
                $roundNumber = $validatedData['round_number'] ?? null;
                $exerciseTime = $validatedData['exercise_time'] ?? null;
                $workoutFormatType = $validatedData['workout_format_type'];
                $workoutFormatId = $validatedData['workout_format_id'];
                $workoutManagerId = $validatedData['workout_manager_id'];
                $roundNumber = $validatedData['round_number'] ?? null;
                $classId = $validatedData['class_Id'] ?? null;

                // Check if record exists
                $query = \App\Models\DailyAccessory::where('member_id', $memberId)
                    ->where('workout_manager_id', $workoutManagerId)
                    ->where('workout_format_type', $workoutFormatType)
                    ->where('workout_format_id', $workoutFormatId)
                    ->where('date', $storedDay)
                    ->where('class_id', $classId)
                    ->where('set_number', $setNumber);
                    /**
                 * If AMRAP format → also check round_number
                 */
                if ($workoutFormatType === 'amrap' && $roundNumber !== null) {
                    $query->where('round_number', $roundNumber);
                }

                $dailyAccessory = $query->first();

                Log::info('Existing accessory record:', ['dailyAccessory' => $dailyAccessory?->toArray()]);

                if ($dailyAccessory) {
                    // Update existing record
                    $dailyAccessory->update([
                        'reps' => $reps,
                        'weight' => $weight,
                        'date' => $storedDay,
                        'round_number' => $roundNumber,
                        'exercise_time' => $exerciseTime,
                        'class_id' => $classId,
                        'notes' => $validatedData['notes'] ?? null,
                    ]);
                    $message = 'Accessory updated successfully';
                    Log::info('Accessory updated', ['workout_manager_id' => $workoutManagerId,'set_number' => $setNumber]);
                } else {
                    // Create new record
                    $dailyAccessory = \App\Models\DailyAccessory::create([
                        'member_id' => $memberId,
                        'workout_manager_id' => $workoutManagerId,
                        'workout_format_type' => $workoutFormatType,
                        'workout_format_id' => $workoutFormatId,
                        'reps' => $reps,
                        'weight' => $weight,
                        'set_number' => $setNumber,
                        'round_number' => $roundNumber,
                        'date' => $storedDay,
                        'exercise_time' => $exerciseTime,
                        'class_id' => $classId,
                        'notes' => $validatedData['notes'] ?? null,
                    ]);
                    $message = 'Accessory saved successfully';
                    Log::info('New accessory record created', ['workout_manager_id' => $workoutManagerId, 'set_number' => $setNumber]);
                }

                $responses[] = [
                    'workout_manager_id' => $workoutManagerId,
                    'set_number' => $setNumber,
                    'round_number' => $roundNumber,
                    'message' => $message,
                    'daily_accessory_id' => $dailyAccessory->id,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Accessory workouts processed successfully',
                'results' => $responses,
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving accessory workout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while saving the accessory workouts',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // public function storestrengthdaily(Request $request)
    // {
    //     Log::info('Received Request Data:', $request->all());

    //     $validated = $request->validate([
    //         'strength_id' => 'required|exists:strengths,id',
    //         'type' => 'required|in:Primary,Alternative',
    //         'reps' => 'required|integer',
    //         'weight' => 'nullable|numeric',
    //     ]);

    //     Log::info('Validated Data:', $validated);

    //     $userId = Auth::id();

    //     if (!$userId) {
    //         Log::warning('User is not authenticated.');
    //         return response()->json(['error' => 'User is not authenticated'], 401);
    //     }

    //     $storedDay = session('selected_day');
    //     if (!$storedDay) {
    //         Log::warning('Selected day is missing from session');
    //         return response()->json(['error' => 'Selected day is missing from session'], 400);
    //     }

    //     try {
    //         // Log the data intended for update or creation
    //         Log::info('Data for updateOrCreate:', [
    //             'member_id' => $userId,
    //             'strength_id' => $validated['strength_id'],
    //             'date' => $storedDay,
    //             'type' => $validated['type'],
    //             'reps' => $validated['reps'],
    //             'weight' => $validated['weight'],
    //         ]);

    //         // Update or create the record
    //         $dailyStrength = DailyStrength::updateOrCreate(
    //             [
    //                 'member_id' => $userId,
    //                 'strength_id' => $validated['strength_id'],
    //                 'date' => $storedDay,
    //             ],
    //             [
    //                 'type' => $validated['type'], // Ensure this is passed correctly
    //                 'reps' => $validated['reps'],
    //                 'weight' => $validated['weight'],
    //             ]
    //         );

    //         Log::info('DailyStrength record updated or created', [
    //             'type' => $validated['type'],
    //             'record' => $dailyStrength->toArray()
    //         ]);

    //         return response()->json(['success' => 'Record saved successfully', 'data' => $dailyStrength], 200);
    //     } catch (\Exception $e) {
    //         Log::error('Error saving DailyStrength record:', ['error' => $e->getMessage()]);
    //         return response()->json(['error' => 'Failed to save record'], 500);
    //     }
    // }



    public function workouttimer()
    {
        $storedDay = session('selected_day');
        // Format the stored day to the desired format
        $date = Carbon::createFromFormat('d/m/Y', $storedDay);
        $dayName = $date->format('l'); // Get the full day name (e.g., Monday)
        $formattedDate = $date->format('d/m/y'); // Format the date

        // Combine day name and date
        $dayWithDate = $formattedDate . ' ' . $dayName;
        //get warup details for specific date
        $tabconditioning = 'conditioning';
        $date = $dayWithDate;
        $detailsconditioning = Conditioning::where('date', $date)
            ->with('workout')
            ->get();

        return view("mobile.user.workout-timer", compact('dayWithDate', 'detailsconditioning'));
    }

    public function histroyview()
    {
        return view("mobile.user.history");
    }

    public function getrainingdaysnclasses(Request $request)
    {
        // default to 'current'
        $mode = strtolower($request->query('mode', $request->query('week', 'current')));
        if (!in_array($mode, ['current', 'previous'])) {
            $mode = 'current';
        }

        $userId = Auth::user()->id;
        $dates = [];

        if ($mode === 'previous') {
            // previous 7 days (7 days before today, up to yesterday)
            $startDate = Carbon::now()->subDays(7)->startOfDay();
        } else {
            // current -> next 7 days starting today
            $startDate = Carbon::now()->startOfDay();
        }

        // Build 7-day list
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $startDate->copy()->addDays($i);
        }

        $classesByDate = [];

        foreach ($dates as $date) {
            $dayName = $date->format('l');
            $formattedDate = $date->format('d/m/Y');
            $dayWithDate = $date->format('d/m/y') . ' ' . $dayName;

            $classes = Classes::where('date', $dayWithDate)
                ->orderBy('time', 'asc')
                ->get();

            $classesByDate[$formattedDate] = [
                'dayName' => $dayName,
                'classes' => $classes->map(function ($class) use ($date, $dayName, $userId) {
                    $isReserved = ReservationSession::where('user_id', $userId)
                        ->where('classes_id', $class->id)
                        ->where('is_reserved', 1)
                        ->exists();

                    return [
                        'id' => $class->id,
                        'time' => \Carbon\Carbon::parse($class->time)->format('g:i A'),
                        'date' => $date->format('d/m/Y'),
                        'dayName' => $dayName,
                        'availablespots' => $class->availablespots,
                        'isReserved' => $isReserved,
                    ];
                })->toArray()
            ];
        }

        return response()->json([
            'success' => true,
            'weekMode' => $mode,
            'dates' => array_map(fn($d) => [
                'date' => $d->format('d/m/Y'),
                'dayName' => $d->format('l')
            ], $dates),
            'classesByDate' => $classesByDate,
            'defaultTime' => '06:00:00'
        ], 200);
    }

    public function storescoremobile(Request $request)
    {
        $validatedData = $request->validate([
            'selected_day' => 'required|string',
            'sleep_input' => 'required|string',
            'alertness_input' => 'required|string',
            'excitement_input' => 'required|string',
            'stress_input' => 'required|string',
            'soreness_input' => 'required|string',
            'score' => 'required|integer',
            'class_id' => 'required|integer',
        ]);

        $user = $request->user();

        // Find an existing score for the same user and date, or create a new one
        $score = $user->scores()->updateOrCreate(
            ['user_id' => $user->id, 'selected_day' => $validatedData['selected_day'],'class_id' => $validatedData['class_id']],
            $validatedData
        );

        return response()->json([
            'success' => true,
            'message' => 'Score saved successfully.',
            'data' => $score
        ], 200);
    }


    public function getexcerise(Request $request){

    }



    public function getscore(Request $request)
    {
        $request->validate([
            'selected_day' => 'required|string',
            'class_id' => 'required|integer',
        ]);

        $user = $request->user();

        $score = $user->scores()
            ->where('selected_day', $request->selected_day)
            ->where('class_id', $request->class_id)
            ->first();

        if ($score) {
            return response()->json([
                'success' => true,
                'message' => 'Score found.',
                'data' => $score
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No score found for this day.',
            'data' => null
        ], 404);
    }


    public function getworkout(Request $request)
    {
        $request->validate([
            'selected_day' => 'required|string', // e.g., "Tuesday 09/09/2025"
            'class_id'     => 'required|integer',
        ]);

        try {
            $user = $request->user();
            $member = Newprofile::where('user_id', $user->id)->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member profile not found for this user.',
                ], 404);
            }

            // Sanitize and normalize incoming date string
            $rawDateString = trim($request->input('selected_day'));
            Log::info('Day received (raw):', ['day' => $rawDateString]);

            // Remove any characters except digits, slashes, spaces, letters and hyphen
            $sanitized = preg_replace('/[^\d\/\sA-Za-z\-]/', '', $rawDateString);
            $sanitized = preg_replace('/\s+/', ' ', trim($sanitized));
            Log::info('Day received (sanitized):', ['day' => $sanitized]);

            // Try to extract a date substring like d/m/y or d/m/Y
            $datePart = null;
            if (preg_match('/\d{1,2}\/\d{1,2}\/\d{2,4}/', $sanitized, $m)) {
                $datePart = $m[0];
            }

            $date = null;
            if ($datePart) {
                // Choose format based on year length
                $fmt = (preg_match('/\/\d{4}$/', $datePart) ? 'd/m/Y' : 'd/m/y');

                try {
                    $date = \Carbon\Carbon::createFromFormat($fmt, $datePart);
                } catch (\Exception $e) {
                    // fallback to parse
                    try {
                        $date = \Carbon\Carbon::parse($datePart);
                    } catch (\Exception $e2) {
                        $date = null;
                    }
                }
            } else {
                // Last resort: try to parse the sanitized string directly
                try {
                    $date = \Carbon\Carbon::parse($sanitized);
                } catch (\Exception $e) {
                    $date = null;
                }
            }

            if (!$date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format for selected_day.',
                    'provided' => $rawDateString
                ], 400);
            }

            // Build normalized patterns (both two-digit and four-digit year, dayname before/after)
            $dayName = $date->format('l');
            $shortDateTwo = $date->format('d/m/y');   // e.g., 23/01/26
            $shortDateFour = $date->format('d/m/Y');  // e.g., 23/01/2026
            $dayWithDate = $shortDateTwo . ' ' . $dayName;
            $dayWithDateNew = $shortDateFour . ' ' . $dayName;
            $dayNameFirst = $dayName . ' ' . $shortDateTwo;
            $dayNameFirstNew = $dayName . ' ' . $shortDateFour;

            $classId = $request->class_id;

            // Search for any assignment containing the date in any reasonable format
            $assignedRaw = WorkoutAssign::where('class_id', $classId)
                ->where(function ($q) use ($shortDateTwo, $shortDateFour, $dayWithDate, $dayWithDateNew, $dayNameFirst, $dayNameFirstNew) {
                    $q->where('date', 'LIKE', '%' . $shortDateTwo . '%')
                      ->orWhere('date', 'LIKE', '%' . $shortDateFour . '%')
                      ->orWhere('date', 'LIKE', '%' . $dayWithDate . '%')
                      ->orWhere('date', 'LIKE', '%' . $dayWithDateNew . '%')
                      ->orWhere('date', 'LIKE', '%' . $dayNameFirst . '%')
                      ->orWhere('date', 'LIKE', '%' . $dayNameFirstNew . '%');
                })
                ->get();

            $assigned = $assignedRaw->groupBy('workout_type');

            Log::info('Assigned workouts', [
                'class_id' => $classId,
                'search_patterns' => [
                    '%' . $shortDateTwo . '%',
                    '%' . $shortDateFour . '%',
                    '%' . $dayWithDate . '%',
                    '%' . $dayWithDateNew . '%',
                    '%' . $dayNameFirst . '%',
                    '%' . $dayNameFirstNew . '%',
                ],
                'assigned_count' => $assigned->map->count()->toArray(),
            ]);

            // Helper function
            $getIds = fn ($type) => isset($assigned[$type])
                ? $assigned[$type]->pluck('workout_id')->toArray()
                : [];

            // Warmup
            $detailswarmup = Warmup::whereIn('id', $getIds('warmup'))
                ->with('workout.categoryOption')
                ->get();

            // Strength
            $detailsstrength = Strength::whereIn('id', $getIds('strength'))
                ->with('sets.strengthing')
                ->with('workout.categoryOption')
                ->get();

            // Conditioning
            $detailsconditioning = Conditioning::whereIn('id', $getIds('conditioning'))
                ->with('workout')
                ->with('workout.categoryOption')
                ->get();

            // Weightlifting
            $detailsweight = Weightlifting::whereIn('id', $getIds('weightlifting'))
                ->with('sets')
                ->with('sets.weightlifting')
                ->with('workout')
                ->with('workout.categoryOption')
                ->get();

            // Test (filtered by member)
            $detailstest = Test::whereIn('id', $getIds('test'))
                ->where('member_id', operator: $member->id)
                ->with('workout.categoryOption')
                ->with('member')
                ->get();

            if ($assigned->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workouts assigned for this class & date',
                    'class_id' => $classId,
                    'date' => $dayWithDate
                ]);
            }

            // Create a map of [workout name + category_options_id] => weight from test
            $testWeights = $detailstest->mapWithKeys(function ($test) {
                $key = $test->workout->workout . '_' . $test->workout->category_options_id;
                return [$key => $test->weight];
            });

            $detailswarmup->transform(function ($item) use ($member, $dayWithDateNew) {
                $completed = DailyWarmup::where('member_id', $member->id)
                    ->where('warmup_id', $item->id)
                    ->where('date', $dayWithDateNew)
                    ->exists();

                $item->workout_completed = $completed ? 1 : 0;
                return $item;
            });

            $detailswarmup->transform(function ($item) use ($member, $dayWithDateNew) {
                $item_completed = DailyWarmup::where('member_id', $member->id)
                    ->where('warmup_id', $item->id)
                    ->where('reps', '>', 0)
                    ->where('date', $dayWithDateNew)
                    ->exists();

                $item->warmup_item_completed = $item_completed ? 1 : 0;
                return $item;
            });

            // Append matching weight to Strength/Conditioning/Weightlifting workouts
            $appendTestWeight = function ($item) use ($testWeights) {
                if ($item->workout) {
                    $key = $item->workout->workout . '_' . $item->workout->category_options_id;
                    $item->test_weight = $testWeights[$key] ?? null;
                } else {
                    $item->test_weight = null;
                }
                return $item;
            };

            $detailsstrength->transform($appendTestWeight);
            $detailsconditioning->transform($appendTestWeight);
            $detailsweight->transform($appendTestWeight);

            $detailsweight->transform(function ($item) use ($member, $dayWithDateNew) {
                $completed = DailyWeightlifting::where('member_id', $member->id)
                    ->where('weightlifting_id', $item->id)
                    ->where('date', $dayWithDateNew)
                    ->exists();

                $item->workout_completed = $completed ? 1 : 0;
                return $item;
            });

            $detailsstrength->transform(function ($item) use ($member, $dayWithDateNew) {
                $completed = DailyStrength::where('member_id', $member->id)
                    ->where('strength_id', $item->id)
                    ->where('date', $dayWithDateNew)
                    ->exists();

                $item->workout_completed = $completed ? 1 : 0;
                return $item;
            });

            $detailsconditioning->transform(function ($item) use ($member, $dayWithDateNew) {
                $completed = DailyConditioning::where('member_id', $member->id)
                    ->where('conditioning_id', $item->id)
                    ->where('date', $dayWithDateNew)
                    ->exists();

                $item->workout_completed = $completed ? 1 : 0;
                return $item;
            });

            $score = $user->scores()
                ->where('selected_day', $request->selected_day)
                ->first();

            $categoryOptions = CategoryOption::select('id', 'category_name')->get();

            // Get all workouts with category option name
            $workoutlibrary = WorkoutLibrary::with('categoryOption:id,category_name')
                ->get(['id', 'category_options_id', 'type', 'workout', 'link'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'workout' => $item->workout,
                        'type' => $item->type,
                        'category_option_id' => $item->category_options_id,
                        'category_option_name' => $item->categoryOption->category_name ?? null,
                    ];
                });

            return response()->json([
                'success' => true,
                'selected_day' => $rawDateString,
                'dayWithDate' => $dayWithDate,
                'warmup' => $detailswarmup,
                'strength' => $detailsstrength,
                'conditioning' => $detailsconditioning,
                'weightlifting' => $detailsweight,
                'test' => $detailstest,
                'score' => $score,
                'workoutlibrary' => $workoutlibrary,
                'categoryOptions' => $categoryOptions,
                'member' => $member
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching workout data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function updateWeight(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:tests,id',
            'workout_id' => 'required|integer|exists:workout_libraries,id',
            'member_id' => 'required|integer|exists:members,id',
            'weight' => 'required|numeric',
        ]);

        $test = Test::where('id', $request->id)
                    ->where('workout_id', $request->workout_id)



                    ->where('member_id', $request->member_id)
                    ->first();

        if (!$test) {
            return response()->json([
                'status' => false,
                'message' => 'No matching test found.',
            ], 404);
        }

        $test->update([
            'weight' => $request->weight,
            'date' => $request->date ?? $test->date,
            'unit_type' => 'kg', // Force unit type to kg on update as per request
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Weight updated successfully.',
        ]);
    }

   public function insertWeight(Request $request)
    {

    $request->validate([
        'workout_id' => 'required|integer',
        'member_id' => 'required|integer',
        'weight' => 'required|numeric',
        'selected_day' => 'required|string',
    ]);

    try {
        $date = Carbon::createFromFormat('l d/m/Y', $request->selected_day);
        $dayWithDate = $date->format('d/m/y l');


        $workoutManager = WorkoutManager::create([
            'workout_name' => $request->workoutname,
            'type_id' => 7, // Default type ID for tests/measurements
            'format_id' => 0,//no specific format type for this
            'date' => $dayWithDate,
            // 'number' can be left null or set if needed.
        ]);

        $test = Test::create([
            'workout_id'  => $request->workout_id,
            'member_id'   => $request->member_id,
            'weight'      => $request->weight,
            'workoutname' => $request->workoutname,
            'date'        => $dayWithDate,
            'category_id' => $request->category_id ?? null,
            'workout_manager_id' => $workoutManager->id,
            'workout_libraries_id' => $request->workout_id,
            'unit_type' => 'kg',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Weight inserted successfully.',
            'data'    => $test,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to insert weight. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
