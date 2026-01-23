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
    public function storewarmupdaily(Request $request, $id = null)
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
                    'warmup_id' => 'required|integer|exists:warmups,id',
                    'reps' => 'required|integer',
                    'selected_day' => 'required|string',
                ]);

                if ($validator->fails()) {
                    $responses[] = [
                        'warmup_id' => $item['warmup_id'] ?? null,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ];
                    continue;
                }

                $validatedData = $validator->validated();
                $storedDay = $validatedData['selected_day'];

                $dailyWarmup = DailyWarmup::where('member_id', $memberId)
                        ->where('warmup_id', $validatedData['warmup_id'])
                        ->where('date', $storedDay)
                        ->first();


                Log::info('Existing warmup record:', ['dailyWarmup' => $dailyWarmup?->toArray()]);

                if ($dailyWarmup) {
                    $dailyWarmup->update([
                        'reps' => $validatedData['reps'],
                        'date' => $storedDay,
                    ]);
                    $message = 'Warm-up updated successfully';
                    Log::info('Warm-up updated', ['warmup_id' => $validatedData['warmup_id']]);
                } else {
                    $dailyWarmup = DailyWarmup::create([
                        'member_id' => $memberId,
                        'warmup_id' => $validatedData['warmup_id'],
                        'reps' => $validatedData['reps'],
                        'date' => $storedDay,
                    ]);
                    $message = 'Warm-up saved successfully';
                    Log::info('New warm-up created', ['warmup_id' => $validatedData['warmup_id']]);
                }

                $responses[] = [
                    'warmup_id' => $validatedData['warmup_id'],
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
                    'strength_id' => 'required|integer|exists:strengths,id',
                    'reps' => 'required|integer',
                    'weight' => 'nullable|numeric',
                    'set_number' => 'required|integer|min:1',
                    'selected_day' => 'required|string',
                ]);

                if ($validator->fails()) {
                    $responses[] = [
                        'strength_id' => $item['strength_id'] ?? null,
                        'set_number' => $item['set_number'] ?? null,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ];
                    continue;
                }

                $validatedData = $validator->validated();
                $storedDay = $validatedData['selected_day'];
                $weight = $validatedData['weight'] ?? null;
                $setNumber = $validatedData['set_number'];

                // Check if record exists for this member + strength_id + date + set_number
                $dailyStrength = DailyStrength::where('member_id', $memberId)
                    ->where('strength_id', $validatedData['strength_id'])
                    ->where('date', $storedDay)
                    ->where('set_number', $setNumber)
                    ->first();

                Log::info('Existing strength record:', ['dailyStrength' => $dailyStrength?->toArray()]);

                if ($dailyStrength) {
                    // Update existing record
                    $dailyStrength->update([
                        'reps' => $validatedData['reps'],
                        'weight' => $weight,
                        'date' => $storedDay,
                    ]);
                    $message = 'Strength updated successfully';
                    Log::info('Strength updated', [
                        'strength_id' => $validatedData['strength_id'],
                        'set_number' => $setNumber
                    ]);
                } else {
                    // Create new record
                    $dailyStrength = DailyStrength::create([
                        'member_id' => $memberId,
                        'strength_id' => $validatedData['strength_id'],
                        'reps' => $validatedData['reps'],
                        'weight' => $weight,
                        'set_number' => $setNumber,
                        'date' => $storedDay,
                    ]);
                    $message = 'Strength saved successfully';
                    Log::info('New strength record created', [
                        'strength_id' => $validatedData['strength_id'],
                        'set_number' => $setNumber
                    ]);
                }

                $responses[] = [
                    'strength_id' => $validatedData['strength_id'],
                    'set_number' => $setNumber,
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
    {
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

            // ✅ Get weightlifting array directly
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
                    'weightlifting_id' => 'required|integer|exists:weightliftings,id',
                    'reps' => 'required|integer',
                    'weight' => 'nullable|numeric',
                    'set_number' => 'required|integer|min:1',
                    'selected_day' => 'required|string',
                ]);

                if ($validator->fails()) {
                    $responses[] = [
                        'weightlifting_id' => $item['weightlifting_id'] ?? null,
                        'set_number' => $item['set_number'] ?? null,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ];
                    continue;
                }

                $validatedData = $validator->validated();
                $storedDay = $validatedData['selected_day'];
                $weight = $validatedData['weight'] ?? null;
                $setNumber = $validatedData['set_number'];

                // ✅ Check if record exists for this member + weightlifting_id + date + set_number
                $dailyWeightlifting = DailyWeightlifting::where('member_id', $memberId)
                    ->where('weightlifting_id', $validatedData['weightlifting_id'])
                    ->where('date', $storedDay)
                    ->where('set_number', $setNumber)
                    ->first();

                Log::info('Existing weightlifting record:', ['dailyWeightlifting' => $dailyWeightlifting?->toArray()]);

                if ($dailyWeightlifting) {
                    // ✅ Update existing record
                    $dailyWeightlifting->update([
                        'reps' => $validatedData['reps'],
                        'weight' => $weight,
                        'date' => $storedDay,
                    ]);
                    $message = 'Weightlifting updated successfully';
                    Log::info('Weightlifting updated', [
                        'weightlifting_id' => $validatedData['weightlifting_id'],
                        'set_number' => $setNumber
                    ]);
                } else {
                    // ✅ Create new record
                    $dailyWeightlifting = DailyWeightlifting::create([
                        'member_id' => $memberId,
                        'weightlifting_id' => $validatedData['weightlifting_id'],
                        'reps' => $validatedData['reps'],
                        'weight' => $weight,
                        'set_number' => $setNumber,
                        'date' => $storedDay,
                    ]);
                    $message = 'Weightlifting saved successfully';
                    Log::info('New weightlifting record created', [
                        'weightlifting_id' => $validatedData['weightlifting_id'],
                        'set_number' => $setNumber
                    ]);
                }

                $responses[] = [
                    'weightlifting_id' => $validatedData['weightlifting_id'],
                    'set_number' => $setNumber,
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

            // ✅ Get conditioning array directly
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
                    'conditioning_id' => 'required|integer|exists:conditionings,id',
                    'reps' => 'required|integer',
                    'weight' => 'nullable|numeric',
                    'selected_day' => 'required|string',
                ]);

                if ($validator->fails()) {
                    $responses[] = [
                        'conditioning_id' => $item['conditioning_id'] ?? null,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ];
                    continue;
                }

                $validatedData = $validator->validated();
                $storedDay = $validatedData['selected_day'];
                $weight = $validatedData['weight'] ?? null;

                // ✅ Check if record exists for this member + conditioning + date
                $dailyConditioning = DailyConditioning::where('member_id', $memberId)
                    ->where('conditioning_id', $validatedData['conditioning_id'])
                    ->where('date', $storedDay)
                    ->first();

                Log::info('Existing conditioning record:', ['dailyConditioning' => $dailyConditioning?->toArray()]);

                if ($dailyConditioning) {
                    // ✅ Update existing record
                    $dailyConditioning->update([
                        'reps' => $validatedData['reps'],
                        'weight' => $weight,
                        'date' => $storedDay,
                    ]);
                    $message = 'Conditioning updated successfully';
                    Log::info('Conditioning updated', ['conditioning_id' => $validatedData['conditioning_id']]);
                } else {
                    // ✅ Create new record
                    $dailyConditioning = DailyConditioning::create([
                        'member_id' => $memberId,
                        'conditioning_id' => $validatedData['conditioning_id'],
                        'reps' => $validatedData['reps'],
                        'weight' => $weight,
                        'date' => $storedDay,
                    ]);
                    $message = 'Conditioning saved successfully';
                    Log::info('New conditioning record created', ['conditioning_id' => $validatedData['conditioning_id']]);
                }

                $responses[] = [
                    'conditioning_id' => $validatedData['conditioning_id'],
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
        $userId = Auth::user()->id;
        $dates = [];
        $today = Carbon::now()->startOfDay();

        // Prepare next 7 days
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $today->copy()->addDays($i);
        }

        $classesByDate = [];

        foreach ($dates as $date) {
            $dayName = $date->format('l');          // Monday, Tuesday, etc.
            $formattedDate = $date->format('d/m/Y'); // For key
            $dayWithDate = $date->format('d/m/y') . ' ' . $dayName; // Matches DB format

            // Get 6AM class for that date
            $classes = Classes::where('date', $dayWithDate)
                            ->orderBy('time', 'asc')
                            ->get();

            // Add class info or null
            $classesByDate[$formattedDate] = [
                'dayName' => $dayName,
                'classes' =>   $classes->isNotEmpty()
                    ? $classes->map(function ($class) use ($date, $dayName, $userId) {
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
                    })
                    : []
            ];
        }

        return response()->json([
            'success' => true,
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
        ]);

        $user = $request->user();

        // Find an existing score for the same user and date, or create a new one
        $score = $user->scores()->updateOrCreate(
            ['user_id' => $user->id, 'selected_day' => $validatedData['selected_day']],
            $validatedData
        );

        return response()->json([
            'success' => true,
            'message' => 'Score saved successfully.',
            'data' => $score
        ], 200);
    }

    public function getscore(Request $request)
    {
        $request->validate([
            'selected_day' => 'required|string',
        ]);

        $user = $request->user();

        $score = $user->scores()
            ->where('selected_day', $request->selected_day)
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
                ->where('member_id', $member->id)
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

        $test = Test::create([
            'workout_id'  => $request->workout_id,
            'member_id'   => $request->member_id,
            'weight'      => $request->weight,
            'workoutname' => $request->workoutname,
            'date'        => $dayWithDate, 
            'category_id' => $request->category_id ?? null,
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
        ], 500);
    }
}


}
