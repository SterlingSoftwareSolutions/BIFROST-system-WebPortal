<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Conditioning;
use App\Models\DailyAccessory;
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
use Illuminate\Support\Str;


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

    public function getStrengthProgresssssssssss(Request $request)
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

    public function getStrengthProgress(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access.'
            ], 401);
        }

        // ✅ Validate input
        $request->validate([
            'workout_id' => 'required|exists:workout_libraries,id',
        ]);

        try {
            $member = Newprofile::where('user_id', $user->id)->first();
            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Member profile not found.'
                ], 404);
            }

            $workoutId = $request->workout_id;
            $allData = collect();

            // List of daily models
            $dailyModels = [
                DailyWarmup::class,
                DailyStrength::class,
                DailyWeightlifting::class,
                DailyConditioning::class,
                DailyAccessory::class,
            ];

            foreach ($dailyModels as $model) {

                $records = $model::where('member_id', $member->id)
                    ->with('workoutFormat') // eager load the correct relation
                    ->get()
                    ->filter(function ($item) use ($workoutId) {
                        $format = $item->workout_format; // use snake_case
                        return $format->workout_libraries_id == $workoutId;
                    });

                foreach ($records as $item) {
                    $format = $item->workout_format;
                    $libraryId = $format ? $format->workout_libraries_id : null;
                    Log::info('Debug record', [
                        'daily_id' => $item->id,
                        'format_type' => $item->workout_format_type,
                        'format_id' => $item->workout_format_id,
                        'library_id' => $libraryId,
                    ]);
                }

                $allData = $allData->merge($records);
            }


            if ($allData->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No workout data found for this workout.',
                    'data' => []
                ]);
            }

            // Prepare graph data
            $graphData = $allData->map(function ($item) {
                try {
                    $date = Carbon::createFromFormat('d/m/y l', $item->date)->format('Y-m-d');
                } catch (\Exception $e) {
                    $date = $item->date;
                }

                return [
                    'date' => $date,
                    'reps' => (int) ($item->reps ?? 0),
                    'weight' => (float) ($item->weight ?? 0),
                ];
            })->sortBy('date')->values();

            return response()->json([
                'status' => 'success',
                'message' => 'Workout progress retrieved successfully.',
                'data' => [
                    'workout_id' => $workoutId,
                    'graph' => [
                        'labels' => $graphData->pluck('date'),
                        'datasets' => [
                            ['label' => 'Reps', 'data' => $graphData->pluck('reps')],
                            ['label' => 'Weight (kg)', 'data' => $graphData->pluck('weight')],
                        ]
                    ],
                    'summary' => [
                        'total_reps' => $allData->sum('reps'),
                        'total_weight' => $allData->sum('weight'),
                        'total_sets' => $allData->count(),
                        'one_rep_max' => round($allData->max(function ($item) {
                            return ($item->weight ?? 0) * (1 + (($item->reps ?? 0)/30));
                        }), 2)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Workout progress error', ['error' => $e->getMessage()]);
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
                    'workoutManager.rounds.workoutLibrary.categoryOption',
                    'workoutManager.amraps.workoutLibrary.categoryOption',
                    'workoutManager.intervals.workoutLibrary.categoryOption',
                    'workoutManager.straights.workoutLibrary.categoryOption',
                    'workoutManager.emoms.workoutLibrary.categoryOption',
                    'workoutManager.circuits.workoutLibrary.categoryOption',
                    'workoutManager.pyramids.workoutLibrary.categoryOption',
                    'workoutManager.forTimes.workoutLibrary.categoryOption',
                ])
                ->orderBy('date', 'asc')
                ->get();

            // Get Warmup records
            $dailyWarmups = DailyWarmup::where('member_id', $member->id)
                ->with([
                    'workoutManager.rounds.workoutLibrary.categoryOption',
                    'workoutManager.amraps.workoutLibrary.categoryOption',
                    'workoutManager.intervals.workoutLibrary.categoryOption',
                    'workoutManager.straights.workoutLibrary.categoryOption',
                    'workoutManager.emoms.workoutLibrary.categoryOption',
                    'workoutManager.circuits.workoutLibrary.categoryOption',
                    'workoutManager.pyramids.workoutLibrary.categoryOption',
                    'workoutManager.forTimes.workoutLibrary.categoryOption',
                ])
                ->orderBy('date', 'asc')
                ->get();

            // Get Weightlifting records
            $dailyWeightliftings = DailyWeightlifting::where('member_id', $member->id)
                ->with([
                    'workoutManager.rounds.workoutLibrary.categoryOption',
                    'workoutManager.amraps.workoutLibrary.categoryOption',
                    'workoutManager.intervals.workoutLibrary.categoryOption',
                    'workoutManager.straights.workoutLibrary.categoryOption',
                    'workoutManager.emoms.workoutLibrary.categoryOption',
                    'workoutManager.circuits.workoutLibrary.categoryOption',
                    'workoutManager.pyramids.workoutLibrary.categoryOption',
                    'workoutManager.forTimes.workoutLibrary.categoryOption',
                ])
                ->orderBy('date', 'asc')
                ->get();

            // Get Conditioning records
            $dailyConditionings = DailyConditioning::where('member_id', $member->id)
                ->with([
                    'workoutManager.rounds.workoutLibrary.categoryOption',
                    'workoutManager.amraps.workoutLibrary.categoryOption',
                    'workoutManager.intervals.workoutLibrary.categoryOption',
                    'workoutManager.straights.workoutLibrary.categoryOption',
                    'workoutManager.emoms.workoutLibrary.categoryOption',
                    'workoutManager.circuits.workoutLibrary.categoryOption',
                    'workoutManager.pyramids.workoutLibrary.categoryOption',
                    'workoutManager.forTimes.workoutLibrary.categoryOption',
                ])
                ->orderBy('date', 'asc')
                ->get();

            // Get Conditioning records
            $dailyAccessory = DailyAccessory::where('member_id', $member->id)
                ->with([
                    'workoutManager.rounds.workoutLibrary.categoryOption',
                    'workoutManager.amraps.workoutLibrary.categoryOption',
                    'workoutManager.intervals.workoutLibrary.categoryOption',
                    'workoutManager.straights.workoutLibrary.categoryOption',
                    'workoutManager.emoms.workoutLibrary.categoryOption',
                    'workoutManager.circuits.workoutLibrary.categoryOption',
                    'workoutManager.pyramids.workoutLibrary.categoryOption',
                    'workoutManager.forTimes.workoutLibrary.categoryOption',
                ])
                ->orderBy('date', 'asc')
                ->get();

            // Format all datasets
            $strengthData = $dailyStrengths->map(function ($item) {
                $manager = $item->workoutManager;

                if (!$manager) {return null;}

                // Detect first available format
                $formatItem =
                    $manager->rounds->first()
                    ?? $manager->amraps->first()
                    ?? $manager->intervals->first()
                    ?? $manager->straights->first()
                    ?? $manager->emoms->first()
                    ?? $manager->circuits->first()
                    ?? $manager->pyramids->first()
                    ?? $manager->forTimes->first();

                $workoutName = null;
                $categoryName = null;
                $trainingload = null;

                if ($formatItem && $formatItem->workoutLibrary) {
                    $workoutName = $formatItem->workoutLibrary->workout;
                    $trainingload = $formatItem->training_load;
                    $categoryName = optional($formatItem->workoutLibrary->categoryOption)->category_name;
                }
                return [
                    'type' => Str::lower(optional($manager->type)->name ?? 'workout'),
                    'date' => $item->date,
                    'weight' => $trainingload,
                    'reps' => $item->reps,
                    'set_number' => $item->set_number,
                    'category_name' => $categoryName,
                    'workout' => $workoutName,
                ];
            })->filter()->values();

            $warmupData = $dailyWarmups->map(function ($item) {
                $manager = $item->workoutManager;

                if (!$manager) {return null;}

                // Detect first available format
                $formatItem =
                    $manager->rounds->first()
                    ?? $manager->amraps->first()
                    ?? $manager->intervals->first()
                    ?? $manager->straights->first()
                    ?? $manager->emoms->first()
                    ?? $manager->circuits->first()
                    ?? $manager->pyramids->first()
                    ?? $manager->forTimes->first();

                $workoutName = null;
                $categoryName = null;
                $trainingload = null;

                if ($formatItem && $formatItem->workoutLibrary) {
                    $workoutName = $formatItem->workoutLibrary->workout;
                    $trainingload = $formatItem->training_load;
                    $categoryName = optional($formatItem->workoutLibrary->categoryOption)->category_name;
                }

                return [
                    'type' => Str::lower(optional($manager->type)->name ?? 'workout'),
                    'date' => $item->date,
                    'weight' => $trainingload,
                    'reps' => $item->reps,
                    'set_number' => $item->set_number,
                    'category_name' => $categoryName,
                    'workout' => $workoutName,
                ];
            })->filter()->values();

            $weightliftingData = $dailyWeightliftings->map(function ($item) {
                $manager = $item->workoutManager;

                if (!$manager) {return null;}

                // Detect first available format
                $formatItem =
                    $manager->rounds->first()
                    ?? $manager->amraps->first()
                    ?? $manager->intervals->first()
                    ?? $manager->straights->first()
                    ?? $manager->emoms->first()
                    ?? $manager->circuits->first()
                    ?? $manager->pyramids->first()
                    ?? $manager->forTimes->first();

                $workoutName = null;
                $categoryName = null;
                $trainingload = null;

                if ($formatItem && $formatItem->workoutLibrary) {
                    $workoutName = $formatItem->workoutLibrary->workout;
                    $trainingload = $formatItem->training_load;
                    $categoryName = optional($formatItem->workoutLibrary->categoryOption)->category_name;
                }
                return [
                    'type' => Str::lower(optional($manager->type)->name ?? 'workout'),
                    'date' => $item->date,
                    'weight' => $trainingload,
                    'reps' => $item->reps,
                    'set_number' => $item->set_number,
                    'category_name' => $categoryName,
                    'workout' => $workoutName,
                ];
            })->filter()->values();

            $conditioningData = $dailyConditionings->map(function ($item) {
                $manager = $item->workoutManager;

                if (!$manager) {return null;}

                // Detect first available format
                $formatItem =
                    $manager->rounds->first()
                    ?? $manager->amraps->first()
                    ?? $manager->intervals->first()
                    ?? $manager->straights->first()
                    ?? $manager->emoms->first()
                    ?? $manager->circuits->first()
                    ?? $manager->pyramids->first()
                    ?? $manager->forTimes->first();

                $workoutName = null;
                $categoryName = null;
                $trainingload = null;

                if ($formatItem && $formatItem->workoutLibrary) {
                    $workoutName = $formatItem->workoutLibrary->workout;
                    $trainingload = $formatItem->training_load;
                    $categoryName = optional($formatItem->workoutLibrary->categoryOption)->category_name;
                }
                return [
                    'type' => Str::lower(optional($manager->type)->name ?? 'workout'),
                    'date' => $item->date,
                    'weight' => $trainingload,
                    'reps' => $item->reps,
                    'set_number' => $item->set_number,
                    'category_name' => $categoryName,
                    'workout' => $workoutName,
                ];
            })->filter()->values();

            // Format all datasets
            $accessoryData = $dailyAccessory->map(function ($item) {
                $manager = $item->workoutManager;

                if (!$manager) {return null;}

                // Detect first available format
                $formatItem =
                    $manager->rounds->first()
                    ?? $manager->amraps->first()
                    ?? $manager->intervals->first()
                    ?? $manager->straights->first()
                    ?? $manager->emoms->first()
                    ?? $manager->circuits->first()
                    ?? $manager->pyramids->first()
                    ?? $manager->forTimes->first();

                $workoutName = null;
                $categoryName = null;
                $trainingload = null;

                if ($formatItem && $formatItem->workoutLibrary) {
                    $workoutName = $formatItem->workoutLibrary->workout;
                    $trainingload = $formatItem->training_load;
                    $categoryName = optional($formatItem->workoutLibrary->categoryOption)->category_name;
                }
                return [
                    'type' => Str::lower(optional($manager->type)->name ?? 'workout'),
                    'date' => $item->date,
                    'weight' => $trainingload,
                    'reps' => $item->reps,
                    'set_number' => $item->set_number,
                    'category_name' => $categoryName,
                    'workout' => $workoutName,
                ];
            })->filter()->values();

            // Combine all workout types
            $combinedData = collect()
                ->merge($strengthData->toBase())
                ->merge($warmupData->toBase())
                ->merge($weightliftingData->toBase())
                ->merge($conditioningData->toBase())
                ->merge($accessoryData->toBase())
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
        try {
            $request->validate([
                'class_id' => 'required|integer',
                'date' => 'required|string',
            ]);

            $user = $request->user();
            $member = Newprofile::where('user_id', $user->id)->first();

            $classId = $request->input('class_id');
            $dateString = $request->input('date');

            // Parse the custom date format "13/02/26 Friday" to a Carbon instance
            try {
                $date = Carbon::createFromFormat('d/m/y l', $dateString);
            } catch (\Exception $e) {
                // Fallback: try other common formats
                try {
                    $date = Carbon::parse($dateString);
                } catch (\Exception $e2) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid date format. Expected format: "dd/mm/yy DayName" (e.g., "13/02/26 Friday")',
                    ], 400);
                }
            }

            // Get workout assignments filtered by class_id and date
            $workoutAssignments = WorkoutAssign::where('class_id', $classId)
                ->where('date', 'LIKE', '%' . $date->format('d/m/y') . '%')
                ->get();

            if ($workoutAssignments->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No workouts found for this class and date',
                    'date' => $dateString,
                    'class_id' => $classId,
                    'workouts' => [],
                ], 200);
            }

            // Get workout IDs from assignments
            $workoutIds = $workoutAssignments->pluck('workout_id')->unique();

            // Fetch workout details from workout_manager with relationships
            $workouts = WorkoutManager::with([
                    'format',
                    'type',
                    'straights.workoutLibrary.categoryOption', 'straights.sets',
                    'rounds.workoutLibrary.categoryOption',
                    'intervals.workoutLibrary.categoryOption',
                    'amraps.workoutLibrary.categoryOption',
                    'emoms.workoutLibrary.categoryOption',
                    'pyramids.workoutLibrary.categoryOption',
                    'circuits.workoutLibrary.categoryOption',
                    'forTimes.workoutLibrary.categoryOption'
                ])
                ->whereIn('id', $workoutIds)
                ->where('status', 'active')
                ->get();

            // Check warmup completion status for each workout using polymorphic format tracking
            $workouts->each(function ($workout) use ($member, $dateString) {
                // Map format relationships to their types
                $formatMapping = [
                    'rounds' => 'rounds',
                    'amraps' => 'amrap',
                    'forTimes' => 'for-time',
                    'intervals' => 'intervals',
                    'emoms' => 'emom',
                    'straights' => 'straight-sets',
                    'circuits' => 'circuit',
                    'pyramids' => 'pyramid',
                ];

                // Check each format type for completion - only tracking is_completed for individual items
                foreach ($formatMapping as $relation => $formatType) {
                    if ($workout->{$relation} && $workout->{$relation}->isNotEmpty()) {
                        foreach ($workout->{$relation} as $formatItem) {
                            // Check if this specific format item is completed
                            $isCompleted = DailyWarmup::where('member_id', $member->id)
                                ->where('workout_manager_id', $workout->id)
                                ->where('workout_format_type', $formatType)
                                ->where('workout_format_id', $formatItem->id)
                                ->where('date', $dateString)
                                ->exists();

                            // Check if this specific format item has reps saved
                            $hasReps = DailyWarmup::where('member_id', $member->id)
                                ->where('workout_manager_id', $workout->id)
                                ->where('workout_format_type', $formatType)
                                ->where('workout_format_id', $formatItem->id)
                                ->where('reps', '>', 0)
                                ->where('date', $dateString)
                                ->exists();

                            // Add completion status to each format item
                            $formatItem->is_completed = $isCompleted ? 1 : 0;
                            $formatItem->has_reps_saved = $hasReps ? 1 : 0;
                        }
                    }
                }

                // Note: Only keeping type_completed and is_completed fields as requested
            });

            // Group workouts by type
            $groupedWorkouts = $workouts->groupBy(function ($workout) {
                return $workout->type->name ?? 'Unknown';
            });

            // Add type-level completion status: type is completed only if ALL format items across ALL workouts in that type are completed
            $groupedWorkouts = $groupedWorkouts->map(function ($typeWorkouts, $typeName) {
                $totalFormatItems = 0;
                $completedFormatItems = 0;

                // Count all format items and completed items across all workouts in this type
                $typeWorkouts->each(function ($workout) use (&$totalFormatItems, &$completedFormatItems) {
                    $formatMapping = [
                        'rounds' => 'rounds',
                        'amraps' => 'amrap',
                        'forTimes' => 'for_time',
                        'intervals' => 'intervals',
                        'emoms' => 'emom',
                        'straights' => 'straight_sets',
                        'circuits' => 'circuits',
                        'pyramids' => 'pyramid',
                    ];

                    foreach ($formatMapping as $relation => $formatType) {
                        if ($workout->{$relation} && $workout->{$relation}->isNotEmpty()) {
                            foreach ($workout->{$relation} as $formatItem) {
                                $totalFormatItems++;
                                if ($formatItem->is_completed == 1) {
                                    $completedFormatItems++;
                                }
                            }
                        }
                    }
                });

                // Add type completion status to each workout in this type
                $typeCompletionStatus = ($totalFormatItems > 0 && $completedFormatItems === $totalFormatItems) ? 1 : 0;
                $typeWorkouts->each(function ($workout) use ($typeCompletionStatus) {
                    $workout->type_completed = $typeCompletionStatus;
                });

                return $typeWorkouts;
            });

            return response()->json([
                'status' => true,
                'date' => $dateString,
                'class_id' => $classId,
                'workouts' => $groupedWorkouts,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
