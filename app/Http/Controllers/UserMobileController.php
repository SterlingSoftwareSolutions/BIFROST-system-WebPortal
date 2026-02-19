<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Conditioning;
use App\Models\DailyConditioning;
use App\Models\DailyStrength;
use App\Models\DailyWarmup;
use App\Models\DailyWeightlifting;
use App\Models\MonthlyImage;
use App\Models\Newprofile;
use App\Models\Strength;
use App\Models\Weightlifting;
use App\Models\WorkoutLibrary;
use App\Models\WorkoutManager;
use App\Models\WorkoutAssign;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Warmup;

class UserMobileController extends Controller
{
    public function viewprofile()
    {
        $user = Auth::user(); // Get the currently authenticated user

        if (!$user) {
            // If the user is not authenticated, return an unauthorized JSON response
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $member = Newprofile::where('user_id', $user->id)->first();

        // Determine profile image (image_paths is a STRING like "profile/xxx.jpg")
        $profileImage = ($member && !empty($member->image_paths))
            ? asset('storage/' . ltrim($member->image_paths, '/'))
            : asset('storage/default-profile.png');

        // Get the current and previous two months
        $months = collect();
        for ($i = 0; $i < 3; $i++) {
            $date = Carbon::now()->subMonths($i);
            $months->push([
                'month' => $date->format('m'),
                'year' => $date->format('Y'),
                'label' => $date->format('F Y')
            ]);
        }

        // Fetch images for the current and previous two months
        $images = MonthlyImage::where('user_id', $user->id)
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->month)->format('Y-m');
            });

        // Convert grouped images into a clean JSON-friendly format
        $formattedImages = $images->map(function ($group) {
            return $group->map(function ($item) {
                return [
                    'id' => $item->id,
                    'month' => Carbon::parse($item->month)->format('F Y'),
                    'front_image' => $item->front_image ? asset('storage/' . $item->front_image) : null,
                    'side_image' => $item->side_image ? asset('storage/' . $item->side_image) : null,
                    'back_image' => $item->back_image ? asset('storage/' . $item->back_image) : null,
                    'created_at' => $item->created_at,
                ];
            });
        });

        // Return JSON response
        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'member' => $member,
            'firstname' => $member?->firstname ?? '',
            'lastname'  => $member?->lastname ?? '',
            'profileImage' => $profileImage,
            'months' => $months,
            'images' => $formattedImages,
        ], 200);
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'month' => 'required|date',
                'front_image' => 'required|image|mimes:jpeg,png,jpg,heic,heif|max:10240',
                'side_image' => 'required|image|mimes:jpeg,png,jpg,heic,heif|max:10240',
                'back_image' => 'required|image|mimes:jpeg,png,jpg,heic,heif|max:10240',
                'user_id' => 'required|exists:users,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        try {
            // Store uploaded images in 'public/images'

            if (!$request->hasFile('front_image')) {
                throw new Exception('Front image file is missing');
            }

            $frontFile = $request->file('front_image');

            $frontImagePath = $frontFile->store('images', 'public');


            if (!$request->hasFile('side_image')) {
                throw new Exception('Side image file is missing');
            }

            $sideFile = $request->file('side_image');

            $sideImagePath = $sideFile->store('images', 'public');


            if (!$request->hasFile('back_image')) {
                throw new Exception('Back image file is missing');
            }

            $backFile = $request->file('back_image');

            $backImagePath = $backFile->store('images', 'public');



            $monthlyImage = MonthlyImage::create([
                'month' => $request->month,
                'front_image' => $frontImagePath,
                'side_image' => $sideImagePath,
                'back_image' => $backImagePath,
                'user_id' => $request->user_id,
            ]);

            // Return JSON success response
            return response()->json([
                'status' => 'success',
                'message' => 'Images uploaded successfully!',
                'data' => [
                    'id' => $monthlyImage->id,
                    'month' => Carbon::parse($monthlyImage->month)->format('F Y'),
                    'front_image' => asset('storage/' . $monthlyImage->front_image),
                    'side_image' => asset('storage/' . $monthlyImage->side_image),
                    'back_image' => asset('storage/' . $monthlyImage->back_image),
                    'user_id' => $monthlyImage->user_id,
                    'created_at' => $monthlyImage->created_at,
                ]
            ], 201);
        } catch (\Exception $e) {


            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload images.',
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ], 500);
        }
    }
     // profile image upload
    public function profileImageStore(Request $request)
            {
                $request->validate([
                    'user_id' => 'required|exists:users,id',
                    'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:10240',
                ]);

                $path = $request->file('profile_image')->store('profile', 'public'); // profile/xxx.jpg

                // if user already has row -> update, else -> insert
                $profile = Newprofile::updateOrCreate(
                    ['user_id' => $request->user_id],
                    ['image_paths' => $path]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Profile photo saved.',
                    'profile_image_url' => asset('storage/' . $path),
                    'data' => $profile,
                ]);
            }














    //get strength and weightlifting type workouts
    public function getStrengthWorkouts()
    {
        try {
            // Fetch all workouts where type = 'strength' or 'weightlifting'
            $workouts = WorkoutLibrary::whereIn('type', ['strength', 'weightlifting'])
                    ->orderBy('workout', 'asc')
                    ->get();

            if ($workouts->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No strength or weightlifting workouts found.',
                    'data' => [],
                ], 200);
            }

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Strength and weightlifting workouts retrieved successfully.',
                'count' => $workouts->count(),
                'data' => $workouts,
            ], 200);
        } catch (Exception $e) {
            // Handle any unexpected errors
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch workouts.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStrengthProgress(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access.'
            ], 401);
        }

        // Validate input
        $request->validate([
            'workout_id' => 'required|exists:workout_libraries,id',
        ]);

        try {
            // Get member ID from user
            $member = Newprofile::where('user_id', $user->id)->first();

            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Member profile not found.'
                ], 404);
            }

            // Get the workout to determine its type
            $workout = WorkoutLibrary::find($request->workout_id);

            if (!$workout) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Workout not found.'
                ], 404);
            }

            $allData = collect();

            Log::info('Strength progress start', [
                'user_id' => $user->id,
                'workout_id' => $request->workout_id,
                'member_id' => optional($member)->id,
                'workout_type' => optional($workout)->type,
            ]);


            // ✅ Get all related strength records for this workout
            if ($workout->type === 'strength') {
                $strengths = Strength::where('workout_id', $request->workout_id)->get();

                   Log::info('Strength records fetched', [
                        'strengths_count' => $strengths->count(),
                        'strength_ids' => $strengths->pluck('id')->take(20), // prevent huge log
                    ]);

                if (!$strengths->isEmpty()) {
                    $strengthIds = $strengths->pluck('id');

                    // Fetch all daily strength records
                    $strengthData = DailyStrength::where('member_id', $member->id)
                        ->whereIn('strength_id', $strengthIds)
                        ->orderBy('date', 'asc')
                        ->get();


                        Log::info('DailyStrength fetched', [
                                'daily_strength_count' => $strengthData->count(),
                                'first_row' => $strengthData->first(), // shows sample
                            ]);

                    $allData = $allData->merge($strengthData);
                }
            }




            // ✅ Get all related weightlifting records for this workout
            if ($workout->type === 'weightlifting') {
                $weightliftings = Weightlifting::where('workout_id', $request->workout_id)->get();

                if (!$weightliftings->isEmpty()) {
                    $weightliftingIds = $weightliftings->pluck('id');

                    // Fetch all daily weightlifting records
                    $weightliftingData = DailyWeightlifting::where('member_id', $member->id)
                        ->whereIn('weightlifting_id', $weightliftingIds)
                        ->orderBy('date', 'asc')
                        ->get();

                    $allData = $allData->merge($weightliftingData);
                }
            }

            if ($allData->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No workout data found for this workout.',
                    'data' => []
                ], 200);
            }

            // ✅ Prepare data for graph
            $graphData = $allData->map(function ($item) {
                try {
                    $date = Carbon::createFromFormat('d/m/y l', $item->date)->format('Y-m-d');
                } catch (\Exception $e) {
                    $date = $item->date;
                }

                return [
                    'date' => $date,
                    'reps' => (int) $item->reps,
                    'weight' => (float) $item->weight,
                ];
            })->sortBy('date')->values();

            // ✅ Calculate summary metrics
            $totalReps = $allData->sum('reps');
            $totalWeight = $allData->sum('weight');
            $totalSets = $allData->count();
            $oneRepMax = $allData->max(function ($item) {
                return $item->weight * (1 + ($item->reps / 30)); // Epley formula
            });

            // ✅ Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Workout progress retrieved successfully.',
                'data' => [
                    'workout_id' => (int) $request->workout_id,
                    'workout_type' => $workout->type,
                    'graph' => [
                        'labels' => $graphData->pluck('date'),
                        'datasets' => [
                            [
                                'label' => 'Reps',
                                'data' => $graphData->pluck('reps'),
                            ],
                            [
                                'label' => 'Weight (kg)',
                                'data' => $graphData->pluck('weight'),
                            ]
                        ]
                    ],
                    'summary' => [
                        'total_reps' => $totalReps,
                        'total_weight' => $totalWeight,
                        'total_sets' => $totalSets,
                        'one_rep_max' => round($oneRepMax, 2)
                    ]
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch workout data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMemberWorkoutDetails(Request $request)
    {
        try {
            $user = Auth::user();
            $member = Newprofile::where('user_id', $user->id)->first();

            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Member profile not found.',
                ], 404);
            }

            // Get Strength records
            $dailyStrengths = DailyStrength::where('member_id', $member->id)
                ->with([
                    'strenght.workout',   // Strength -> WorkoutLibrary
                    'strenght.category',  // Strength -> CategoryOption
                ])
                ->orderBy('date', 'asc')
                ->get();

            // Get Warmup records
            $dailyWarmups = DailyWarmup::where('member_id', $member->id)
                ->with([
                    'warmup.workout',     // Warmup -> WorkoutLibrary
                    'warmup.category',    // Warmup -> CategoryOption
                ])
                ->orderBy('date', 'asc')
                ->get();

            // Get Weightlifting records
            $dailyWeightliftings = DailyWeightlifting::where('member_id', $member->id)
                ->with([
                    'weightlifting.workout',   // Weightlifting -> WorkoutLibrary
                    'weightlifting.category',  // Weightlifting -> CategoryOption
                ])
                ->orderBy('date', 'asc')
                ->get();

            // Get Conditioning records
            $dailyConditionings = DailyConditioning::where('member_id', $member->id)
                ->with([
                    'conditioning.workout',   // Conditioning -> WorkoutLibrary
                    'conditioning.category',  // Conditioning -> CategoryOption
                ])
                ->orderBy('date', 'asc')
                ->get();

            // Format all datasets
            $strengthData = $dailyStrengths->map(function ($item) {
                return [
                    'type' => 'strength',
                    'date' => $item->date,
                    'weight' => $item->weight,
                    'reps' => $item->reps,
                    'category_name' => $item->strenght && $item->strenght->category ? $item->strenght->category->category_name : null,
                    'workout' => $item->strenght && $item->strenght->workout ? $item->strenght->workout->workout : null,
                ];
            });

            $warmupData = $dailyWarmups->map(function ($item) {
                return [
                    'type' => 'warmup',
                    'date' => $item->date,
                    'weight' => $item->warmup ? $item->warmup->weight : null,
                    'reps' => $item->reps,
                    'category_name' => $item->warmup && $item->warmup->category ? $item->warmup->category->category_name : null,
                    'workout' => $item->warmup && $item->warmup->workout ? $item->warmup->workout->workout : null,
                ];
            });

            $weightliftingData = $dailyWeightliftings->map(function ($item) {
                return [
                    'type' => 'weightlifting',
                    'date' => $item->date,
                    'weight' => $item->weight,
                    'reps' => $item->reps,
                    'set_number' => $item->set_number,
                    'category_name' => $item->weightlifting && $item->weightlifting->category ? $item->weightlifting->category->category_name : null,
                    'workout' => $item->weightlifting && $item->weightlifting->workout ? $item->weightlifting->workout->workout : null,
                ];
            });

            $conditioningData = $dailyConditionings->map(function ($item) {
                return [
                    'type' => 'conditioning',
                    'date' => $item->date,
                    'weight' => $item->weight,
                    'reps' => $item->reps,
                    'category_name' => $item->conditioning && $item->conditioning->category ? $item->conditioning->category->category_name : null,
                    'workout' => $item->conditioning && $item->conditioning->workout ? $item->conditioning->workout->workout : null,
                ];
            });

            // Combine all workout types
            $combinedData = $strengthData
                ->merge($warmupData)
                ->merge($weightliftingData)
                ->merge($conditioningData)
                ->sortBy('date')
                ->values();

            return response()->json([
                'status' => 'success',
                'data' => $combinedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch workout details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMemberProfile()
    {
        try {
            $user = Auth::user();

            // Check authentication
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.'
                ], 401);
            }

            // Find member profile linked to this user
            $member = Newprofile::where('user_id', $user->id)->first();

            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Member profile not found for this user.'
                ], 404);
            }

            // Return combined user + member data
            return response()->json([
                'status' => 'success',
                'message' => 'Member profile retrieved successfully.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'member' => [
                        'id' => $member->id,
                        'firstname' => $member->firstname,
                        'lastname' => $member->lastname,
                        'dob' => $member->dob,
                        'gender' => $member->gender,
                        'age' => $member->age,
                        'phone' => $member->phone,
                        'email' => $member->email,
                        'address' => $member->address,
                        'height' => $member->height,
                        'weight' => $member->weight,
                        'bmr' => $member->bmr,
                        'primary_goal' => $member->primary_goal,
                        'subscription_level' => $member->subscription_level,
                        'startdate' => $member->startdate,
                        'is_subsactive' => (bool) $member->is_subsactive,
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch member profile.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateMemberProfile(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access.'
                ], 401);
            }

            // Validate inputs
            $validated = $request->validate([
                'firstname' => 'sometimes|string|max:255',
                'lastname' => 'sometimes|string|max:255',
                'dob' => 'sometimes|date',
                'gender' => 'sometimes|string',
                'age' => 'sometimes|integer',
                'phone' => 'sometimes|string|max:20',
                'email' => 'sometimes|email|max:255',
                'address' => 'sometimes|string',
                'height' => 'sometimes|numeric',
                'weight' => 'sometimes|numeric',
                'bmr' => 'sometimes|numeric',
                'primary_goal' => 'sometimes|string',
                'subscription_level' => 'sometimes|string',
                'startdate' => 'sometimes|date',
                'is_subsactive' => 'sometimes|boolean',
            ]);

            // Get member record
            $member = Newprofile::where('user_id', $user->id)->first();

            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Member profile not found.'
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | 1. UPDATE USER (name + email)
            |--------------------------------------------------------------------------
            */
            if ($request->has('email')) {
                $user->email = $request->email;
            }

            if ($request->has('username')) {
                $user->name = $request->username;
            }

            $user->save();

            /*
            |--------------------------------------------------------------------------
            | 2. UPDATE MEMBER TABLE
            |--------------------------------------------------------------------------
            */

            $member->update([
                'firstname' => $request->firstname ?? $member->firstname,
                'lastname' => $request->lastname ?? $member->lastname,
                'dob' => $request->dob ?? $member->dob,
                'gender' => $request->gender ?? $member->gender,
                'age' => $request->age ?? $member->age,
                'phone' => $request->phone ?? $member->phone,
                'email' => $request->email ?? $member->email,
                'address' => $request->address ?? $member->address,
                'height' => $request->height ?? $member->height,
                'weight' => $request->weight ?? $member->weight,
                'bmr' => $request->bmr ?? $member->bmr,
                'primary_goal' => $request->primary_goal ?? $member->primary_goal, // uses accessor/mutator
                'subscription_level' => $request->subscription_level ?? $member->subscription_level,
                'startdate' => $request->startdate ?? $member->startdate,
                'is_subsactive' => $request->is_subsactive ?? $member->is_subsactive,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully.',
                'data' => [
                    'user' => $user,
                    'member' => $member
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getWorkouts(Request $request)
    {
        log::info('getWorkouts called with payload', [
            'payload' => $request->all(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        try {
            $request->validate([
                'class_id' => 'required|integer',
                // 'date' => 'required|string', // expecting format "19/02/26 Thursday" - validation handled below with custom parsing
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access.',
                ], 401);
            }

            $member = Newprofile::where('user_id', $user->id)->first();
            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Member profile not found for this user.',
                ], 404);
            }

            $classId = $request->input('class_id');
            // $date = $request->input('date'); // Original simple date

            // --- START Logic from MobileController::getworkout ---

             // Sanitize and normalize incoming date string
             $rawDateString = trim($request->input('date')); // Changed from 'selected_day' to 'date' to match input
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
 
             $dateObj = null; // Renamed to avoid conflict with $date input
             if ($datePart) {
                 // Choose format based on year length
                 $fmt = (preg_match('/\/\d{4}$/', $datePart) ? 'd/m/Y' : 'd/m/y');
 
                 try {
                     $dateObj = \Carbon\Carbon::createFromFormat($fmt, $datePart);
                 } catch (\Exception $e) {
                     // fallback to parse
                     try {
                         $dateObj = \Carbon\Carbon::parse($datePart);
                     } catch (\Exception $e2) {
                         $dateObj = null;
                     }
                 }
             } else {
                 // Last resort: try to parse the sanitized string directly
                 try {
                     $dateObj = \Carbon\Carbon::parse($sanitized);
                 } catch (\Exception $e) {
                     $dateObj = null;
                 }
             }
 
             if (!$dateObj) {
                 return response()->json([
                     'status' => false, // varied from success: false
                     'message' => 'Invalid date format.',
                     'provided' => $rawDateString
                 ], 400);
             }

            // Build normalized patterns (both two-digit and four-digit year, dayname before/after)
            $dayName = $dateObj->format('l');
            $shortDateTwo = $dateObj->format('d/m/y');   // e.g., 23/01/26
            $shortDateFour = $dateObj->format('d/m/Y');  // e.g., 23/01/2026
            $dayWithDate = $shortDateTwo . ' ' . $dayName;
            $dayWithDateNew = $shortDateFour . ' ' . $dayName;
            $dayNameFirst = $dayName . ' ' . $shortDateTwo;
            $dayNameFirstNew = $dayName . ' ' . $shortDateFour;

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

             // Helper function
             $getIds = fn ($type) => isset($assigned[$type])
             ? $assigned[$type]->pluck('workout_id')->toArray()
             : [];

            // Fetch Data for new response parts
            // Warmup
            $detailswarmup = \App\Models\Warmup::whereIn('id', $getIds('warmup'))
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
           $detailstest = \App\Models\Test::whereIn('id', $getIds('test'))
           ->where('member_id', $member->id)
           ->with('workout.categoryOption')
           ->with('member')
           ->get();

            

            // if no test is assigned via class, check for individual test assignments for this member and date
            if ($detailstest->isEmpty()) {
                $detailstest = \App\Models\Test::where('member_id', $member->id)
                    ->where(function ($q) use ($dayWithDate, $dayWithDateNew) {
                        $q->where('date', $dayWithDate)
                        ->orWhere('date', $dayWithDateNew);
                    })
                    ->with('workout.categoryOption')
                    ->with('member')
                    ->get();
            }

            // Map through each test detail to append type_id from Type table
            $detailstest->map(function ($test) use ($dayWithDate) {
                if ($test->workout) {
                    $typeValue = $test->workout->type; // Get type from WorkoutLibrary relation
                    // Find Type record
                    $typeRecord = \App\Models\Type::where('name', $typeValue)->first();
                    
                    // Append to test object
                    $test->type_id = $typeRecord ? $typeRecord->id : null;
                    $test->type_name = $typeValue;

                    if ($test->type_id) {
                        $workoutManagers = \App\Models\WorkoutManager::where('type_id', $test->type_id)
                            ->where('date', $dayWithDate)
                            ->get(); // Get all matching workout managers

                        $test->workout_managers = $workoutManagers;
                    }
                }
                return $test;
            });

             // Create a map of [workout name + category_options_id] => weight from test
             $testWeights = $detailstest->mapWithKeys(function ($test) {
                if ($test->workout) {
                    $key = $test->workout->workout . '_' . $test->workout->category_options_id;
                    return [$key => $test->weight];
                }
                return [];
            });

            // Mark completions
            $detailswarmup->transform(function ($item) use ($member, $dayWithDateNew) {
                $item->workout_completed = DailyWarmup::where('member_id', $member->id)
                    ->where('warmup_id', $item->id)
                    ->where('date', $dayWithDateNew)
                    ->exists() ? 1 : 0;

                $item->warmup_item_completed = DailyWarmup::where('member_id', $member->id)
                    ->where('warmup_id', $item->id)
                    ->where('reps', '>', 0)
                    ->where('date', $dayWithDateNew)
                    ->exists() ? 1 : 0;
                return $item;
            });

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
                $item->workout_completed = DailyWeightlifting::where('member_id', $member->id)
                    ->where('weightlifting_id', $item->id)
                    ->where('date', $dayWithDateNew)
                    ->exists() ? 1 : 0;
                return $item;
            });

            $detailsstrength->transform(function ($item) use ($member, $dayWithDateNew) {
                $item->workout_completed = DailyStrength::where('member_id', $member->id)
                    ->where('strength_id', $item->id)
                    ->where('date', $dayWithDateNew)
                    ->exists() ? 1 : 0;
                return $item;
            });

            $detailsconditioning->transform(function ($item) use ($member, $dayWithDateNew) {
                $item->workout_completed = DailyConditioning::where('member_id', $member->id)
                    ->where('conditioning_id', $item->id)
                    ->where('date', $dayWithDateNew)
                    ->exists() ? 1 : 0;
                return $item;
            });

            // Scores
            // The score logic in MobileController uses 'selected_day' from request or session. 
            // We'll use the date we parsed.
            // MobileController stores simple string in DB? 'selected_day' column in scores
            // It seems it uses the raw string input usually. Let's try to match 
            /* $score = $user->scores()
                ->where('selected_day', $rawDateString) // Use raw input as it seems to be the key
                ->first(); */
            
            // Category Options
             $categoryOptions = \App\Models\CategoryOption::select('id', 'category_name')->get();

            // Workout Library
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

            // --- END Logic from MobileController::getworkout ---


            // --- EXISTING Logic for WorkoutManager (UserMobileController) ---

            log::info('Fetching workouts for class_id: ' . $classId . ' on date: ' . $rawDateString);
            
            // Re-using the assignedRaw we already fetched which is more robust than the original single whereDate
            // Original:
            // $workoutAssignments = WorkoutAssign::where('class_id', $classId)
            //    ->whereDate('date', "19/02/26 Thursday") // This looked hardcoded in the file I read!
            //    ->get();
            
            // We will use $assignedRaw IDs for fetching WorkoutManager items
            // However, the original code used `WorkoutAssign` to get `workout_id`.
            // IMPORTANT: In `WorkoutAssign` table, `workout_id` can point to `WorkoutManager`, OR `Warmup`, `Strength` etc depending on `workout_type`.
            // The original UserMobileController logic assumed ALL assignments were for WorkoutManager?
            // "getWorkouts" in UserMobileController lines 714: $workoutIds = $workoutAssignments->pluck('workout_id');
            // Then lines 718: WorkoutManager::whereIn('id', $workoutIds)
            
            // If the `WorkoutAssign` table mixes types, we must only pick those where workout_type implies WorkoutManager?
            // Or does UserMobileController only care about the new WorkoutManager types?
            
            // Based on typical system evolution, `WorkoutManager` is the new system.
            // Let's filter `$assignedRaw` for items that might be WorkoutManager.
            // If `workout_type` is NOT warmup/strength/etc, it might be a format?
            // Or maybe existing logic was just grabbing everything and assuming it is WorkoutManager.
            
            // To be safe and "without affecting current functions", we should try to replicate the exact IDs it would have found.
            // The original used `whereDate('date', "19/02/26 Thursday")` which was weirdly specific. 
            // I assume that was a debug artifact and it SHOULD have used `$date`.
            
            // Let's use the IDs from our robust search, but try to fetch WorkoutManager objects for them.
            $allAssignedIds = $assignedRaw->pluck('workout_id')->unique();
            
            $workouts = WorkoutManager::with([
                    'format',
                    'type',
                    'straights.workoutLibrary', 'straights.sets',
                    'rounds.workoutLibrary.categoryOption',
                    'intervals.workoutLibrary.categoryOption',
                    'amraps.workoutLibrary.categoryOption',
                    'emoms.workoutLibrary.categoryOption',
                    'pyramids.workoutLibrary.categoryOption',
                    'circuits.workoutLibrary.categoryOption',
                    'forTimes.workoutLibrary.categoryOption'
                ])
                ->whereIn('id', $allAssignedIds)
                ->where('status', 'active')
                ->get();

            // Group workouts by type
            $groupedWorkouts = $workouts->groupBy(function ($workout) {
                return $workout->type->name ?? 'Unknown';
            });


            return response()->json([
                'status' => true,
                'date' => $rawDateString,
                'class_id' => $classId,
                
                // Existing key
                'workouts' => $groupedWorkouts,

                // New keys from MobileController
                'dayWithDate' => $dayWithDate,
                'warmup' => $detailswarmup,
                'strength' => $detailsstrength,
                'conditioning' => $detailsconditioning,
                'weightlifting' => $detailsweight,
                'test' => $detailstest,
                //'score' => $score,
                'workoutlibrary' => $workoutlibrary,
                'categoryOptions' => $categoryOptions,
                'member' => $member
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }


}
