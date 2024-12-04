<?php

namespace App\Http\Controllers;

use App\Models\ClientManagement;
use App\Models\Conditioning;
use App\Models\DailyStrength;
use App\Models\DailyWarmup;
use App\Models\Newprofile;
use Exception;
use Carbon\Carbon;
use App\Models\Score;
use App\Models\Strength;
use App\Models\UserScore;
use App\Models\Warmup;
use App\Models\Weightlifting;
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
        if (!Session::has('selected_day')) {
            session(['selected_day' => Carbon::now()->format('d/m/Y')]);
        }

        $dates = [];
        $startOfWeek = Carbon::now()->startOfWeek(); // Get Monday

        for ($i = 0; $i < 7; $i++) {
            $dates[] = $startOfWeek->copy()->addDays($i)->format('d/m/Y');
        }

        // Get the selected day from the session
        $selectedDay = session('selected_day');

        return view("mobile.user.trainingday", compact('dates', 'selectedDay'));
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
        //dd($storedDay);
        // Format the stored day to the desired format
        $date = Carbon::createFromFormat('d/m/Y', $storedDay);
        $dayName = $date->format('l'); // Get the full day name (e.g., Monday)
        $formattedDate = $date->format('d/m/y'); // Format the date

        // Combine day name and date
        $dayWithDate = $formattedDate . ' ' . $dayName;

        //get warup details for specific date
        $tabwarmup = 'warmup';
        $date = $dayWithDate;
        $detailswarmup = Warmup::where('date', $dayWithDate)
            ->with('workouts')
            ->with('workouts.categoryOption')
            ->get();

        // //get warup details for specific date
        $tabstrength = 'strength';
        $date = $dayWithDate;
        $detailsstrength = Strength::where('date', $date)
            ->with('sets')
            ->with('sets.strengthing')
            ->with('workout')
            ->with('workout.categoryOption')
            ->get();

        //  //get warup details for specific date
        //  $tabconditioning = 'conditioning';
        //  $date = $dayWithDate;
        //  $detailsconditioning = ClientManagement::where('tab', $tabconditioning)
        //      ->where('date', $date)
        //      ->with('workouts')
        //      ->get();


        //  //get warup details for specific date
        $tabweightweight = 'weightlifting';
        $date = $dayWithDate;
        $detailsweight = Weightlifting::where('date', $date)
            ->with('sets')
            ->with('sets.weightlifting')
            ->with('workouts')
            ->with('workouts.categoryOption')
            ->get();

        //  foreach ($detailsweight as $weightlifting) {
        //     foreach ($weightlifting->sets as $set) {
        //         dd($set->sets, $set->reps); // Dump and display the values of sets and reps
        //     }
        // }

        //dd($detailsstrength);
        //return view('mobile.user.workout', compact('dayWithDate','detailswarmup','detailsstrength','detailsconditioning','detailsweight'));
        return view('mobile.user.workout', compact('dayWithDate', 'detailswarmup', 'detailsstrength', 'detailsweight'));
    }

    //store daily warmup workout after clicking
    public function storewarmupdaily(Request $request, $id = null)
    {
        try {
            Log::info('storewarmupdaily function called.');

            $storedDay = session('selected_day');
            Log::info('Stored day from session: ' . $storedDay);

            // Format the stored day to the desired format
            $date = Carbon::createFromFormat('d/m/Y', $storedDay);
            Log::info('Formatted date: ' . $date);

            $dayName = $date->format('l'); // Get the full day name (e.g., Monday)
            $formattedDate = $date->format('d/m/y'); // Format the date

            // Combine day name and date
            $dayWithDate = $formattedDate . ' ' . $dayName;
            Log::info('Day with date: ' . $dayWithDate);

            $validatedData = $request->validate([
                'warmup_id' => 'required|integer|exists:warmups,id',
                'reps' => 'required|integer',
            ]);
            Log::info('Validated data: ', $validatedData);

            $userId = Auth::user()->id;
            $memberId = Newprofile::where('user_id', $userId)->value('id');
            Log::info('Authenticated user ID: ' . $userId);

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
                    'date' => $dayWithDate,
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
            'type' => 'required|in:Primary,Alternative',
            'reps' => 'required|integer',
            'weight' => 'nullable|numeric', // Add validation rule for weight
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
        $storedDay = session('selected_day');
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
                ->where('date', $dayWithDate)
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
                    'date' => $dayWithDate,
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
                    'date' => $dayWithDate,
                    'reps' => $validated['reps'],
                    'weight' => $weight, // Save weight
                ]);
                $message = 'Data successfully saved';

                // Log the creation action with weight included
                Log::info('Created New DailyStrength Record:', [
                    'user_id' => $memberId,
                    'strength_id' => $validated['strength_id'],
                    'date' => $dayWithDate,
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
}
