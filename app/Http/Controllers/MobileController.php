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

            // $storedDay = session('selected_day');
            // Log::info('Stored day from session: ' . $storedDay);

            // // Format the stored day to the desired format
            // $date = Carbon::createFromFormat('d/m/Y', $storedDay);
            // Log::info('Formatted date: ' . $date);

            // $dayName = $date->format('l'); // Get the full day name (e.g., Monday)
            // $formattedDate = $date->format('d/m/y'); // Format the date

            // // Combine day name and date
            // $dayWithDate = $formattedDate . ' ' . $dayName;
            // Log::info('Day with date: ' . $dayWithDate);

            $validatedData = $request->validate([
                'warmup_id' => 'required|integer|exists:warmups,id',
                'reps' => 'required|integer',
                'selected_day' => 'required|string',
            ]);
            Log::info('Validated data: ', $validatedData);

            $userId = Auth::user()->id;
            $memberId = Newprofile::where('user_id', $userId)->value('id');
            Log::info('Authenticated user ID: ' . $userId);
            $storedDay = $validatedData['selected_day'];
            // Check if a record already exists for this user and workout
            $dailyWarmup = DailyWarmup::where('member_id', $memberId)
                ->where('warmup_id', $validatedData['warmup_id'])
                ->first();
            Log::info('Existing DailyWarmup record: ', ['dailyWarmup' => $dailyWarmup]);

            if ($dailyWarmup) {
                Log::info('Warm-up updated111: ', ['dailyWarmup' => $dailyWarmup]);
                // Update the existing record
                $dailyWarmup->update(['reps' => $validatedData['reps']]);
                $dailyWarmup->touch(); // Update the timestamps
                $message = 'Warm-up updated successfully';
                Log::info('Warm-up updated: ', ['dailyWarmup' => $dailyWarmup]);
            } else {
                Log::info('New warm-up created11: ', ['dailyWarmup' => $dailyWarmup]);
                DailyWarmup::create([
                    'member_id' => $memberId,
                    'warmup_id' => $validatedData['warmup_id'],
                    'reps' => $validatedData['reps'],
                    'date' => $storedDay,
                ]);
                $message = 'Warm-up saved successfully';
                Log::info('New warm-up created: ', ['dailyWarmup' => $dailyWarmup]);
            }

            return response()->json([
                'success' => $message,
                'daily_warmup_id' => $dailyWarmup->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving warm-up: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the warm-up'], 500);
        }
    }

    public function storestrengthdaily(Request $request)
    {
        // Log the received request data
        Log::info('Received Request Data:', $request->all());

        // Validate the incoming request data
        $validated = $request->validate([
            'strength_id' => 'required|exists:strengths,id',
            'reps' => 'required|integer',
            'weight' => 'nullable|numeric', // Add validation rule for weight
            'selected_day' => 'required|string',
        ]);

        // Log the validated data
        Log::info('Validated Data:', $validated);

        // Retrieve the authenticated user ID
        $userId = Auth::id();
        $memberId = Newprofile::where('user_id', $userId)->value('id');
        if (!$memberId) {
            Log::warning('User is not authenticated.');
            return response()->json(['error' => 'User is not authenticated'], 401);
        }

        // Retrieve and format the date from session
        $storedDay = $validated['selected_day'];
        if (!$storedDay) {
            Log::warning('Selected day is missing from session');
            return response()->json(['error' => 'Selected day is missing from session'], 400);
        }

        try {

            $storedDay = session('selected_day');
            Log::info('Stored day from session: ' . $storedDay);

            // Format the stored day to the desired format
            $date = Carbon::createFromFormat('d/m/Y', $storedDay);
            Log::info('Formatted date: ' . $date);

            $dayName = $date->format('l'); // Get the full day name (e.g., Monday)
            $formattedDate = $date->format('d/m/y'); // Format the date

            // Combine day name and date
            $dayWithDate = $formattedDate . ' ' . $dayName;

            // $date = Carbon::createFromFormat('d/m/Y', $storedDay);
            // $formattedDate = $date->format('d/m/Y'); // Correct format

            // Log the formatted date
            Log::info('Formatted Date:', ['date' => $formattedDate]);

            // Ensure weight is a float and handle null value
            $weight = isset($validated['weight']) ? floatval($validated['weight']) : null;

            // Log the weight being saved
            Log::info('Weight Value:', ['weight' => $weight]);

            // Check if a record already exists for this user and workout and type
            $dailyStrength = DailyStrength::where('member_id', $memberId)
                ->where('strength_id', $validated['strength_id'])
                ->where('date', $storedDay)
                ->where('type', $validated['type'])
                ->first();

            if ($dailyStrength) {
                // Update existing record
                $dailyStrength->update([
                    'reps' => $validated['reps'],
                    'weight' => $weight, // Update weight
                    'type' => $validated['type'], // Ensure type is updated
                ]);
                $message = 'Data successfully updated';

                // Log the update action
                Log::info('Updated DailyStrength Record:', [
                    'user_id' => $memberId,
                    'strength_id' => $validated['strength_id'],
                    'date' => $storedDay,
                    'weight' => $weight,
                    'type' => $validated['type'],
                    'updated_data' => array_merge($validated, ['weight' => $weight]) // Include weight in log
                ]);
            } else {
                // Create a new record
                DailyStrength::create([
                    'member_id' => $memberId,
                    'strength_id' => $validated['strength_id'],
                    'type' => $validated['type'],
                    'date' => $storedDay,
                    'reps' => $validated['reps'],
                    'weight' => $weight, // Save weight
                ]);
                $message = 'Data successfully saved';

                // Log the creation action with weight included
                Log::info('Created New DailyStrength Record:', [
                    'user_id' => $memberId,
                    'strength_id' => $validated['strength_id'],
                    'date' => $storedDay,
                    'weight' => $weight,
                    'type' => $validated['type'],
                    'created_data' => array_merge($validated, ['weight' => $weight]) // Include weight in log
                ]);
            }

            // Respond with success message
            return response()->json([
                'message' => $message,
                'data' => $validated
            ]);
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Error saving strength: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'An error occurred while saving the strength'], 500);
        }
    }
    public function storeweightliftingdaily(Request $request)
    {
        // Log the received request data
        Log::info('Received Request Data:', $request->all());

        // Validate the incoming request data
        $validated = $request->validate([
            'weightlifting_id' => 'required|exists:weightliftings,id',
            'reps' => 'required|integer',
            'weight' => 'nullable|numeric', // Add validation rule for weight
            'selected_day' => 'required|string',
        ]);

        // Log the validated data
        Log::info('Validated Data:', $validated);

        // Retrieve the authenticated user ID
        $userId = Auth::id();
        $memberId = Newprofile::where('user_id', $userId)->value('id');
        if (!$memberId) {
            Log::warning('User is not authenticated.');
            return response()->json(['error' => 'User is not authenticated'], 401);
        }

        // Retrieve and format the date from session
        $storedDay = $validated['selected_day'];
        if (!$storedDay) {
            Log::warning('Selected day is missing from session');
            return response()->json(['error' => 'Selected day is missing from session'], 400);
        }

        try {

            $storedDay = session('selected_day');
            Log::info('Stored day from session: ' . $storedDay);

            // Format the stored day to the desired format
            $date = Carbon::createFromFormat('d/m/Y', $storedDay);
            Log::info('Formatted date: ' . $date);

            $dayName = $date->format('l'); // Get the full day name (e.g., Monday)
            $formattedDate = $date->format('d/m/y'); // Format the date

            // Combine day name and date
            $dayWithDate = $formattedDate . ' ' . $dayName;

            // $date = Carbon::createFromFormat('d/m/Y', $storedDay);
            // $formattedDate = $date->format('d/m/Y'); // Correct format

            // Log the formatted date
            Log::info('Formatted Date:', ['date' => $formattedDate]);

            // Ensure weight is a float and handle null value
            $weight = isset($validated['weight']) ? floatval($validated['weight']) : null;

            // Log the weight being saved
            Log::info('Weight Value:', ['weight' => $weight]);

            // Check if a record already exists for this user and workout and type
            $dailyWeightlifting  = DailyWeightlifting::where('member_id', $memberId)
                ->where('weightlifting_id', $validated['weightlifting_id'])
                ->where('date', $storedDay)
                ->first();

            if ($dailyWeightlifting) {
                // Update existing record
                $dailyWeightlifting->update([
                    'reps' => $validated['reps'],
                    'weight' => $weight, // Update weight
                ]);
                $message = 'Data successfully updated';

                // Log the update action
                Log::info('Updated dailyWeightlifting Record:', [
                    'user_id' => $memberId,
                    'weightlifting_id' => $validated['weightlifting_id'],
                    'date' => $storedDay,
                    'weight' => $weight,
                    'updated_data' => array_merge($validated, ['weight' => $weight]) // Include weight in log
                ]);
            } else {
                // Create a new record
                DailyWeightlifting::create([
                    'member_id' => $memberId,
                    'weightlifting_id' => $validated['weightlifting_id'],
                    'date' => $storedDay,
                    'reps' => $validated['reps'],
                    'weight' => $weight, // Save weight
                ]);
                $message = 'Data successfully saved';

                // Log the creation action with weight included
                Log::info('Created New DailyWeightlifting Record:', [
                    'user_id' => $memberId,
                    'weightlifting_id' => $validated['weightlifting_id'],
                    'date' => $storedDay,
                    'weight' => $weight,
                    'created_data' => array_merge($validated, ['weight' => $weight]) // Include weight in log
                ]);
            }

            // Respond with success message
            return response()->json([
                'message' => $message,
                'data' => $validated
            ]);
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Error saving weightlifting: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'An error occurred while saving the weightlifting'], 500);
        }
    }

    public function storeconditioningdaily(Request $request)
    {
        // Log the received request data
        Log::info('Received Request Data:', $request->all());

        // Validate the incoming request data
        $validated = $request->validate([
            'conditioning_id' => 'required|exists:conditionings,id',
            'reps' => 'required|integer',
            'weight' => 'nullable|numeric', // Add validation rule for weight
            'selected_day' => 'required|string',
        ]);

        // Log the validated data
        Log::info('Validated Data:', $validated);

        // Retrieve the authenticated user ID
        $userId = Auth::id();
        $memberId = Newprofile::where('user_id', $userId)->value('id');
        if (!$memberId) {
            Log::warning('User is not authenticated.');
            return response()->json(['error' => 'User is not authenticated'], 401);
        }

        // Retrieve and format the date from session
        $storedDay = $validated['selected_day'];
        if (!$storedDay) {
            Log::warning('Selected day is missing from session');
            return response()->json(['error' => 'Selected day is missing from session'], 400);
        }

        try {

            $storedDay = session('selected_day');
            Log::info('Stored day from session: ' . $storedDay);

            // Format the stored day to the desired format
            $date = Carbon::createFromFormat('d/m/Y', $storedDay);
            Log::info('Formatted date: ' . $date);

            $dayName = $date->format('l'); // Get the full day name (e.g., Monday)
            $formattedDate = $date->format('d/m/y'); // Format the date

            // Combine day name and date
            $dayWithDate = $formattedDate . ' ' . $dayName;

            // $date = Carbon::createFromFormat('d/m/Y', $storedDay);
            // $formattedDate = $date->format('d/m/Y'); // Correct format

            // Log the formatted date
            Log::info('Formatted Date:', ['date' => $formattedDate]);

            // Ensure weight is a float and handle null value
            $weight = isset($validated['weight']) ? floatval($validated['weight']) : null;

            // Log the weight being saved
            Log::info('Weight Value:', ['weight' => $weight]);

            // Check if a record already exists for this user and workout and type
            $dailyConditioning  = DailyConditioning::where('member_id', $memberId)
                ->where('conditioning_id', $validated['conditioning_id'])
                ->where('date', $storedDay)
                ->first();

            if ($dailyConditioning) {
                // Update existing record
                $dailyConditioning->update([
                    'reps' => $validated['reps'],
                    'weight' => $weight, // Update weight
                ]);
                $message = 'Data successfully updated';

                // Log the update action
                Log::info('Updated dailyConditioning Record:', [
                    'user_id' => $memberId,
                    'conditioning_id' => $validated['conditioning_id'],
                    'date' => $storedDay,
                    'weight' => $weight,
                    'updated_data' => array_merge($validated, ['weight' => $weight]) // Include weight in log
                ]);
            } else {
                // Create a new record
                DailyConditioning::create([
                    'member_id' => $memberId,
                    'conditioning_id' => $validated['conditioning_id'],
                    'date' => $storedDay,
                    'reps' => $validated['reps'],
                    'weight' => $weight, // Save weight
                ]);
                $message = 'Data successfully saved';

                // Log the creation action with weight included
                Log::info('Created New DailyConditioning Record:', [
                    'user_id' => $memberId,
                    'conditioning_id' => $validated['conditioning_id'],
                    'date' => $storedDay,
                    'weight' => $weight,
                    'created_data' => array_merge($validated, ['weight' => $weight]) // Include weight in log
                ]);
            }

            // Respond with success message
            return response()->json([
                'message' => $message,
                'data' => $validated
            ]);
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Error saving conditioning: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'An error occurred while saving the conditioning'], 500);
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

            // Convert the incoming date string to Carbon
            $dateString = $request->input('selected_day');
            $date = \Carbon\Carbon::createFromFormat('l d/m/Y', $dateString);

            // Build formats
            $dayName = $date->format('l');
            $formattedDate = $date->format('d/m/y');
            $dayWithDate = $formattedDate . ' ' . $dayName;

            // Warmup
            $detailswarmup = Warmup::where('date', $dayWithDate)
                ->where('is_assigned', 1)
                ->with('workout')
                ->with('workout.categoryOption')
                ->get();

            // Strength
            $detailsstrength = Strength::where('date', $dayWithDate)
                ->where('is_assigned', 1)
                ->with('sets')
                ->with('sets.strengthing')
                ->with('workout')
                ->with('workout.categoryOption')
                ->get();

            // Conditioning
            $detailsconditioning = Conditioning::where('date', $dayWithDate)
                ->where('is_assigned', 1)
                ->with('workout')
                ->with('workout.categoryOption')
                ->get();

            // Weightlifting
            $detailsweight = Weightlifting::where('date', $dayWithDate)
                ->where('is_assigned', 1)
                ->with('sets')
                ->with('sets.weightlifting')
                ->with('workout')
                ->with('workout.categoryOption')
                ->get();

            // Test (filtered by member)
            $detailstest = Test::where('date', $dayWithDate)
                ->where('member_id', $member->id)
                ->with('workout')
                ->with('workout.categoryOption')
                ->with('member')
                ->get();

            // Create a map of [workout name + category_options_id] => weight from test
            $testWeights = $detailstest->mapWithKeys(function ($test) {
                $key = $test->workout->workout . '_' . $test->workout->category_options_id;
                return [$key => $test->weight];
            });

            // Append matching weight to Strength workouts
            $detailsstrength->transform(function ($item) use ($testWeights) {
                if ($item->workout) {
                    $key = $item->workout->workout . '_' . $item->workout->category_options_id;
                    $item->test_weight = isset($testWeights[$key]) ? $testWeights[$key] : null;
                } else {
                    $item->test_weight = null;
                }
                return $item;
            });

            // Append matching weight to Conditioning workouts
            $detailsconditioning->transform(function ($item) use ($testWeights) {
                if ($item->workout) {
                    $key = $item->workout->workout . '_' . $item->workout->category_options_id;
                    $item->test_weight = isset($testWeights[$key]) ? $testWeights[$key] : null;
                } else {
                    $item->test_weight = null;
                }
                return $item;
            });

            // Append matching weight to Weightlifting workouts
            $detailsweight->transform(function ($item) use ($testWeights) {
                if ($item->workout) {
                    $key = $item->workout->workout . '_' . $item->workout->category_options_id;
                    $item->test_weight = isset($testWeights[$key]) ? $testWeights[$key] : null;
                } else {
                    $item->test_weight = null;
                }
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
                'selected_day' => $dateString,
                'dayWithDate' => $dayWithDate,
                'warmup' => $detailswarmup,
                'strength' => $detailsstrength,
                'conditioning' => $detailsconditioning,
                'weightlifting' => $detailsweight,
                'test' => $detailstest,
                'score' => $score,
                'workoutlibrary' => $workoutlibrary,
                'categoryOptions' => $categoryOptions,
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

}
