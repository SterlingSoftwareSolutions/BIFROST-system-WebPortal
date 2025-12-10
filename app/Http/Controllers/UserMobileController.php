<?php

namespace App\Http\Controllers;

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
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
}
