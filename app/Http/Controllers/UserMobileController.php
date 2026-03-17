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
            /* $workouts = WorkoutLibrary::whereIn('type', ['strength', 'weightlifting', 'warmup', 'conditioning'])
                    ->orderBy('workout', 'asc')
                    ->get(); */

            $workouts = WorkoutLibrary::orderBy('workout', 'asc')->get();//get all

            log::info('Fetched workouts', ['count' => $workouts, 'types' => $workouts->pluck('type')->unique()]);

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
                    ->with('workoutFormat',
                    'workoutManager.type') // eager load the correct relation
                    ->get()
                    ->filter(function ($item) use ($workoutId) {
                        $format = $item->workout_format; // use snake_case
                        return $format->workout_libraries_id == $workoutId;
                    });

                foreach ($records as $item) {
                    $format = $item->workout_format;
                    $libraryId = $format ? $format->workout_libraries_id : null;
                    if ($item instanceof DailyWarmup) {
                        $weight = optional($item->workout_format)->training_load ?? 0;
                    } else {
                        $weight = $item->weight ?? 0;
                    }
                    Log::info('Debug record', [
                        'daily_id' => $item->id,
                        'format_type' => $item->workout_format_type,
                        'format_id' => $item->workout_format_id,
                        'library_id' => $libraryId,
                        'type' => optional($item->workoutManager->type)->name ?? null,
                        'weight' => $weight,
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
                // ✅ If DailyWarmup → get weight from workoutFormat->trainingload
                if ($item instanceof DailyWarmup) {
                    $weight = optional($item->workout_format)->training_load ?? 0;
                } else {
                    $weight = $item->weight ?? 0;
                }

                return [
                    'date' => $date,
                    'reps' => (int) ($item->reps ?? 0),
                    'weight' => (float) $weight,
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
                        'total_weight' => $allData->sum(function ($item) {
                            if ($item instanceof DailyWarmup) {
                                return optional($item->workout_format)->training_load ?? 0;
                            }
                            return $item->weight ?? 0;
                        }),
                        'total_sets' => $allData->count(),
                        'one_rep_max' => round(
                            $allData->max(function ($item) {

                                if ($item instanceof DailyWarmup) {
                                    $weight = optional($item->workout_format)->training_load ?? 0;
                                } else {
                                    $weight = $item->weight ?? 0;
                                }

                                $reps = $item->reps ?? 0;

                                return $weight * (1 + ($reps / 30));
                            }),
                        2)
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
                ->orderBy('date', 'desc')
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
                ->orderBy('date', 'desc')
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
                ->orderBy('date', 'desc')
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
                ->sortByDesc('date')
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
            'date'     => 'required|string', // e.g. "13/02/26 Friday"
        ]);

        $user = $request->user();
        $member = Newprofile::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json([
                'status'  => false,
                'message' => 'Member profile not found for this user.'
            ], 404);
        }

        $classId    = (int) $request->input('class_id');
        $dateString = trim($request->input('date'));

        log::info('[getWorkouts] Request received', [
            'user_id' => $user->id,
            'member_id' => $member->id,
            'class_id' => $classId,
            'date_string' => $dateString,
        ]);

        try {
            // Convert "20/02/26 Friday" -> proper YYYY-MM-DD
            $dateObj = Carbon::createFromFormat('d/m/y l', $request->date);
            $formattedDate = $dateObj->format('Y-m-d');

            // Query the scores table using the correct column (e.g., created_at)
            $score = $user->scores()
                ->whereDate('created_at', $formattedDate)  // <- replace 'created_at' if your column name differs
                ->first();

        } catch (\Exception $e) {
            $score = null; // if the date parsing fails, just set score as null
        }

        /*
        |--------------------------------------------------------------------------
        | Parse date string -> Carbon
        |--------------------------------------------------------------------------
        */
        try {
            $dateObj = Carbon::createFromFormat('d/m/y l', $dateString);

            log::info('[getWorkouts] Date parsed successfully', [
                'date_string' => $dateString,
                'date_obj' => $dateObj->toDateString(),
            ]);

        } catch (\Exception $e) {
            try {
                $dateObj = Carbon::parse($dateString);
            } catch (\Exception $e2) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Invalid date format. Expected format: "dd/mm/yy DayName" (e.g., "13/02/26 Friday")',
                    'provided' => $dateString,
                ], 400);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Build date patterns for varchar "date" columns (tests, assignments, etc.)
        |--------------------------------------------------------------------------
        */
        $dayName       = $dateObj->format('l');
        $shortDateTwo  = $dateObj->format('d/m/y');
        $shortDateFour = $dateObj->format('d/m/Y');

        $patterns = [
            $dateString,
            $shortDateTwo,
            $shortDateFour,
            $shortDateTwo . ' ' . $dayName,
            $shortDateFour . ' ' . $dayName,
            $dayName . ' ' . $shortDateTwo,
            $dayName . ' ' . $shortDateFour,
        ];

        Log::info('[getWorkouts] START', [
            'user_id' => $user->id,
            'member_id' => $member->id,
            'class_id' => $classId,
            'date_string' => $dateString,
            'patterns' => $patterns,
        ]);

        /*
        |--------------------------------------------------------------------------
        | PART A (ADDED): Fetch test rows for that day + member, build map by library
        |--------------------------------------------------------------------------
        */
        Log::info('[getWorkouts][PART A] Fetching testsForDay...', [
            'member_id' => $member->id,
            'class_id' => $classId,
            'date_string' => $dateString,
        ]);

        $testsForDay = \App\Models\Test::where('member_id', $member->id)
            ->where(function ($q) use ($patterns) {
                foreach ($patterns as $p) {
                    $q->orWhere('date', 'LIKE', '%' . $p . '%');
                }
            })
            ->whereNotNull('workout_libraries_id')
            ->get();

        Log::info('[getWorkouts][PART A] testsForDay retrieved', [
            'count' => $testsForDay->count(),
            'library_ids' => $testsForDay->pluck('workout_libraries_id')->unique()->values()->toArray(),
            'test_ids' => $testsForDay->pluck('id')->values()->toArray(),
        ]);

        // workout_libraries_id => latest test details (latest wins)
        $testMap = [];

        $testsSorted = $testsForDay->sortBy(function ($t) {
            return $t->created_at ? $t->created_at->timestamp : $t->id;
        });

        foreach ($testsSorted as $t) {
            $testMap[$t->workout_libraries_id] = [
                'test_id'         => $t->id,
                'weight'          => $t->weight,
                'unit_type'       => $t->unit_type,
                'date'            => $t->date,
                'test_created_at' => $t->created_at,
            ];
        }
            //include data of workout library and test all in the map
        Log::info('[getWorkouts][PART A] testMap built (latest per workout_libraries_id)', [
            'testMap_keys' => array_keys($testMap),
            'testMap_sample' => array_slice($testMap, 0, 5, true), // first 5 only
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get workout assignments filtered by class_id and date (more flexible)
        |--------------------------------------------------------------------------
        */
        Log::info('[getWorkouts] Fetching workoutAssignments...', [
            'class_id' => $classId,
            'date_string' => $dateString,
        ]);

        $workoutAssignments = WorkoutAssign::where('class_id', $classId)
            ->where(function ($q) use ($patterns) {
                foreach ($patterns as $p) {
                    $q->orWhere('date', 'LIKE', '%' . $p . '%');
                }
            })
            ->get();

        Log::info('[getWorkouts] workoutAssignments retrieved', [
            'count' => $workoutAssignments->count(),
            'workout_ids' => $workoutAssignments->pluck('workout_id')->unique()->values()->toArray(),
            'types' => $workoutAssignments->pluck('workout_type')->unique()->values()->toArray(),
        ]);

        if ($workoutAssignments->isEmpty()) {
            Log::warning('[getWorkouts] No workout assignments found', [
                'class_id' => $classId,
                'date_string' => $dateString,
            ]);

            return response()->json([
                'status'   => true,
                'message'  => 'No workouts found for this class and date',
                'date'     => $dateString,
                'class_id' => $classId,
                'workouts' => [],
                'test_data'=> [
                    'strength'      => [],
                    'weightlifting' => [],
                    'conditioning'  => [],
                ],
                'raw_tests_for_day' => $testsForDay,
            ], 200);
        }

        $workoutIds = $workoutAssignments->pluck('workout_id')->unique();

        /*
        |--------------------------------------------------------------------------
        | Fetch workout details from workout_manager with relationships
        |--------------------------------------------------------------------------
        */
        Log::info('[getWorkouts] Fetching WorkoutManager...', [
            'workout_ids' => $workoutIds->values()->toArray(),
        ]);

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

        Log::info('[getWorkouts] WorkoutManager retrieved', [
            'count' => $workouts->count(),
            'ids' => $workouts->pluck('id')->values()->toArray(),
            'types' => $workouts->pluck('type.name')->unique()->values()->toArray(),
            'formats' => $workouts->pluck('format.slug')->unique()->values()->toArray(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | PART B (UPDATED): Attach test weight/unit/id to matching format rows
        |--------------------------------------------------------------------------
        */
        $formatRelations = [
            'rounds',
            'amraps',
            'forTimes',
            'for_times', // safe for serialization differences
            'intervals',
            'emoms',
            'straights',
            'circuits',
            'pyramids',
        ];

        Log::info('[getWorkouts][PART B] Attaching test data to workout format items...', [
            'format_relations' => $formatRelations,
            'testMap_keys' => array_keys($testMap),
        ]);

        $attachStats = [
            'total_rows_seen' => 0,
            'rows_with_lib_id' => 0,
            'rows_matched' => 0,
            'rows_not_matched' => 0,
            'missing_lib_id' => 0,
            'per_relation' => [],
        ];

        $workouts->each(function ($wm) use ($formatRelations, $testMap, &$attachStats, $testsForDay) {

            foreach ($formatRelations as $rel) {

                if (!isset($attachStats['per_relation'][$rel])) {
                    $attachStats['per_relation'][$rel] = [
                        'seen' => 0,
                        'matched' => 0,
                        'not_matched' => 0,
                        'empty' => 0,
                    ];
                }

                if (!isset($wm->$rel) || !$wm->$rel || $wm->$rel->isEmpty()) {
                    $attachStats['per_relation'][$rel]['empty']++;
                    continue;
                }

                foreach ($wm->$rel as $row) {

                    $attachStats['total_rows_seen']++;
                    $attachStats['per_relation'][$rel]['seen']++;

                    $libId = $row->workout_libraries_id ?? null;

                    // keep stable keys for frontend
                    $row->test_weight = null;
                    $row->test_unit_type = null;
                    $row->test_id = null;
                    $row->test_created_at = null;

                    if (!$libId) {
                        $attachStats['missing_lib_id']++;

                        Log::warning('[getWorkouts][PART B] Format row missing workout_libraries_id', [
                            'workout_manager_id' => $wm->id,
                            'relation' => $rel,
                            'row_id' => $row->id ?? null,
                        ]);

                        continue;
                    }

                    $attachStats['rows_with_lib_id']++;

                    if (isset($testMap[$libId])) {

                        // Attach test values using correct setAttribute for serialization
                        $row->setAttribute('test_weight',     $testMap[$libId]['weight'] ?? null);
                        $row->setAttribute('test_unit_type',  $testMap[$libId]['unit_type'] ?? null);
                        $row->setAttribute('test_id',         $testMap[$libId]['test_id'] ?? null);
                        $row->setAttribute('test_created_at', $testMap[$libId]['test_created_at'] ?? null);

                        /*
                        |--------------------------------------------------------------------------
                        | NEW: Attach universal format row identity (works for ALL formats)
                        |--------------------------------------------------------------------------
                        */
                        $row->setAttribute('format_row_id',   $row->id ?? null);   // always the row id
                        $row->setAttribute('format_relation', $rel);              // rounds/amraps/emoms/etc.
                        $row->setAttribute('format_table',    $rel);              // alias (frontend friendly)

                        $attachStats['rows_matched']++;
                        $attachStats['per_relation'][$rel]['matched']++;

                        Log::info('[getWorkouts][PART B] MATCH: attached test to format row', [
                            'workout_manager_id'    => $wm->id,
                            'relation'             => $rel,
                            'format_row_id'         => $row->id ?? null,
                            'workout_libraries_id' => $libId,

                            'attached_test_id'     => $row->getAttribute('test_id'),
                            'attached_test_weight' => $row->getAttribute('test_weight'),
                            'attached_unit_type'   => $row->getAttribute('test_unit_type'),
                            'row_data'             => $row->toArray(),
                        ]);

                        // UPDATE: Also attach this row info to the actual Test object in $testsForDay
                        // so it appears in "test_data" in the JSON response
                        $matchedTestId = $testMap[$libId]['test_id'] ?? null;
                        if ($matchedTestId) {
                            $testObj = $testsForDay->firstWhere('id', $matchedTestId);
                            if ($testObj) {
                                $testObj->setAttribute('format_row_id', $row->id ?? null);
                                $testObj->setAttribute('format_relation', $rel);
                                $testObj->setAttribute('row_data', $row->toArray());
                            }
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Straight Sets: Attach also inside each set row
                        |--------------------------------------------------------------------------
                        */
                        if (isset($row->sets) && $row->sets && $row->sets->isNotEmpty()) {

                            foreach ($row->sets as $set) {

                                $set->setAttribute('test_weight',     $row->getAttribute('test_weight'));
                                $set->setAttribute('test_unit_type',  $row->getAttribute('test_unit_type'));
                                $set->setAttribute('test_id',         $row->getAttribute('test_id'));
                                $set->setAttribute('test_created_at', $row->getAttribute('test_created_at'));

                                // NEW universal identity for each set also
                                $set->setAttribute('format_row_id',   $row->getAttribute('format_row_id'));
                                $set->setAttribute('format_relation', $rel);
                            }
                        }

                    } else {

                    $attachStats['rows_not_matched']++;
                    $attachStats['per_relation'][$rel]['not_matched']++;

                    Log::info('[getWorkouts][PART B] NO MATCH: no test found for format row', [
                        'workout_manager_id' => $wm->id,
                        'relation'           => $rel,
                        'format_row_id'      => $row->id ?? null,
                        'workout_libraries_id' => $libId,
                        'available_test_library_ids' => array_keys($testMap),
                    ]);
                }
                }
            }
        });

        Log::info('[getWorkouts][PART B] Attach summary', $attachStats);

        /*
        |--------------------------------------------------------------------------
        | Warmup / completion status (your existing logic)
        |--------------------------------------------------------------------------
        */
        $workouts->each(function ($workout) use ($member, $dateString, $dateObj) {

            $typeName = strtolower($workout->type->name ?? '');

                switch ($typeName) {
                    case 'strength':
                        $dailyModel = \App\Models\DailyStrength::class;
                        break;
                    case 'weightlifting':
                        $dailyModel = \App\Models\DailyWeightlifting::class;
                        break;
                    case 'accessory':
                        $dailyModel = \App\Models\DailyAccessory::class;
                        break;
                    case 'conditioning':
                        $dailyModel = \App\Models\DailyConditioning::class;
                        break;
                    default:
                        $dailyModel = \App\Models\DailyWarmup::class;
                        break;
                }

            $formatMapping = [
                'rounds'     => 'rounds',
                'amraps'     => 'amrap',
                'forTimes'   => 'for-time',
                'intervals'  => 'intervals',
                'emoms'      => 'emom',
                'straights'  => 'straight-sets',
                'circuits'   => 'circuit',
                'pyramids'   => 'pyramid',
            ];

            foreach ($formatMapping as $relation => $formatType) {

                if (!$workout->{$relation} || $workout->{$relation}->isEmpty()) {
                    continue;
                }

                foreach ($workout->{$relation} as $formatItem) {
                    // --- AMRAP Special Handling ---
                    if (($workout->format->slug ?? '') === 'amrap') {
                        Log::info('[getWorkouts][Completion Check][AMRAP] Processing format item');

                        $roundEntries = $dailyModel::where('member_id', $member->id)
                            ->where('workout_manager_id', $workout->id)
                            ->where('workout_format_type', $formatType)
                            ->where('workout_format_id', $formatItem->id)
                            ->where('date', $dateString)
                            ->pluck('round_number');

                            Log::info('Retrieved round entries', [
                                'member_id' => $member->id,
                                'workout_manager_id' => $workout->id,
                                'workout_format_type' => $formatType,
                                'workout_format_id' => $formatItem->id,
                                'round_entries' => $roundEntries->toArray(),
                            ]);

                        $roundsDone = 0;
                            $roundsTotal = 0;

                            foreach ($roundEntries as $round) {

                                if (!$round) {
                                    continue;
                                }

                                if (str_contains($round, '/')) {

                                    [$done, $total] = explode('/', $round);

                                    Log::info('Parsed round progress', [
                                        'round' => $round,
                                        'done' => $done,
                                        'total' => $total,
                                    ]);

                                    $roundsDone = max($roundsDone, (int)$done);
                                    $roundsTotal = (int)$total;
                                }
                            }

                            Log::info('Updated rounds done/total', [
                                'rounds_done' => $roundsDone,
                                'rounds_total' => $roundsTotal,
                            ]);

                        $completionPercent = $roundsTotal > 0
                            ? round(($roundsDone / $roundsTotal) * 100)
                            : 0;

                        $formatItem->rounds_done = $roundsDone;
                        $formatItem->rounds_total = $roundsTotal;
                        $formatItem->completion_percent = $completionPercent;
                    }
                    elseif (($workout->format->slug ?? '') === 'straight-sets') {

                        if ($formatItem->sets && $formatItem->sets->isNotEmpty()) {
                            foreach ($formatItem->sets as $set) {

                                $query = $dailyModel::where('member_id', $member->id)
                                    ->where('workout_manager_id', $workout->id)
                                    ->where('workout_format_type', $formatType)
                                    ->where('workout_format_id', $set->id)
                                    ->where('date', $dateString);

                                $dailyQuery = (clone $query);
                                $existsInDaily = $dailyQuery->exists();

                                if ($existsInDaily) {
                                    $dailyRepsSum = $dailyQuery->sum('reps');
                                    $targetReps = $set->res ?? 0;

                                    $isCompleted = ($dailyRepsSum >= $targetReps);

                                    $set->is_completed = $isCompleted ? 1 : 0;
                                    $set->daily_reps = $dailyRepsSum;
                                    $set->target_reps = $targetReps;
                                } else {
                                    $set->is_completed = 0;
                                    $set->daily_reps = 0;
                                    $set->target_reps = $set->res ?? 0;
                                }
                            }
                        }

                        unset($formatItem->is_completed);
                        unset($formatItem->has_reps_saved);

                    } else {

                        $baseQuery = $dailyModel::where('member_id', $member->id)
                            ->where('workout_manager_id', $workout->id)
                            ->where('workout_format_type', $formatType)
                            ->where('workout_format_id', $formatItem->id)
                            ->where('date', $dateString);

                        if (
                            in_array($typeName, ['strength', 'weightlifting', 'accessory'])
                            && isset($formatItem->sets)
                            && $formatItem->sets
                            && $formatItem->sets->isNotEmpty()
                        ) {

                            $isCompleted = $baseQuery->exists();

                            foreach ($formatItem->sets as $set) {
                                $set->is_completed = $isCompleted ? 1 : 0;
                            }

                            $formatItem->is_completed = $isCompleted ? 1 : 0;
                            unset($formatItem->has_reps_saved);

                        } else {

                            $formatId = $formatItem->id;
                            $dailyQuery = (clone $baseQuery);
                            
                            // Only proceed with rep logic/logging if the user has actually logged something for this exercise today
                            if ($dailyQuery->exists()) {
                                $dailyRepsSum = $dailyQuery->sum('reps');
                                $targetReps = $formatItem->reps ?? 0;

                                $isCompleted = ($dailyRepsSum >= $targetReps);

                                Log::info('Workout completion check:', [
                                    'table_name'               => (new $dailyModel)->getTable(),
                                    'workout_format_id_target' => $formatId,
                                    'date_string'              => $dateString,
                                    'dailyReps'                => $dailyRepsSum,
                                    'targetReps'               => $targetReps,
                                    'isCompleted'              => $isCompleted
                                ]);

                                $hasReps = $dailyQuery->where('reps', '>', 0)->exists();

                                $formatItem->is_completed   = $isCompleted ? 1 : 0;
                                $formatItem->has_reps_saved = $hasReps ? 1 : 0;
                                $formatItem->daily_reps      = $dailyRepsSum;
                                $formatItem->target_reps     = $targetReps;
                            } else {
                                // If no record exists in the daily table, it's definitely not completed
                                $formatItem->is_completed   = 0;
                                $formatItem->has_reps_saved = 0;
                                $formatItem->daily_reps      = 0;
                                $formatItem->target_reps     = $formatItem->reps ?? 0;
                            }
                        }
                    }
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Group workouts by type + calculate type_completed
        |--------------------------------------------------------------------------
        */
        $groupedWorkouts = $workouts->groupBy(function ($workout) {
            return $workout->type->name ?? 'Unknown';
        });

        $groupedWorkouts = $groupedWorkouts->map(function ($typeWorkouts) {

            $totalFormatItems = 0;
            $completedFormatItems = 0;

            $typeWorkouts->each(function ($workout) use (&$totalFormatItems, &$completedFormatItems) {

                $formatRelations = [
                    'rounds',
                    'amraps',
                    'forTimes',
                    'intervals',
                    'emoms',
                    'straights',
                    'circuits',
                    'pyramids',
                ];

                foreach ($formatRelations as $relation) {

                    if (!$workout->{$relation} || $workout->{$relation}->isEmpty()) {
                        continue;
                    }

                    foreach ($workout->{$relation} as $formatItem) {
                      Log::info('[getWorkouts][Completion Check] Processing format item', [
                            $formatItem->toArray(),  ]);

                        if ($relation === 'straights') {
                            if ($formatItem->sets && $formatItem->sets->isNotEmpty()) {
                                foreach ($formatItem->sets as $set) {
                                    $totalFormatItems++;
                                    if (($set->is_completed ?? 0) == 1) {
                                        $completedFormatItems++;
                                    }
                                }
                            }
                        }  else {
                            $totalFormatItems++;
                            if (($formatItem->is_completed ?? 0) == 1) {
                                $completedFormatItems++;
                            }
                        }
                    }
                }
            });

            $typeCompletionStatus = ($totalFormatItems > 0 && $completedFormatItems === $totalFormatItems) ? 1 : 0;

            $typeWorkouts->each(function ($workout) use ($typeCompletionStatus) {
                $workout->type_completed = $typeCompletionStatus;
            });

            return $typeWorkouts;
        });

        /*
        |--------------------------------------------------------------------------
        | Your existing "search bar filtering / date normalization" block (unchanged)
        |--------------------------------------------------------------------------
        */
        $rawDateString = trim($request->input('date'));
        Log::info('Day received (raw):', ['day' => $rawDateString]);

        $sanitized = preg_replace('/[^\d\/\sA-Za-z\-]/', '', $rawDateString);
        $sanitized = preg_replace('/\s+/', ' ', trim($sanitized));
        Log::info('Day received (sanitized):', ['day' => $sanitized]);

        $datePart = null;
        if (preg_match('/\d{1,2}\/\d{1,2}\/\d{2,4}/', $sanitized, $m)) {
            $datePart = $m[0];
        }

        $dateObj2 = null;
        if ($datePart) {
            $fmt = (preg_match('/\/\d{4}$/', $datePart) ? 'd/m/Y' : 'd/m/y');
            try {
                $dateObj2 = Carbon::createFromFormat($fmt, $datePart);
            } catch (\Exception $e) {
                try {
                    $dateObj2 = Carbon::parse($datePart);
                } catch (\Exception $e2) {
                    $dateObj2 = null;
                }
            }
        } else {
            try {
                $dateObj2 = Carbon::parse($sanitized);
            } catch (\Exception $e) {
                $dateObj2 = null;
            }
        }

        if (!$dateObj2) {
            return response()->json([
                'status'   => false,
                'message'  => 'Invalid date format.',
                'provided' => $rawDateString
            ], 400);
        }

        $dayName2       = $dateObj2->format('l');
        $shortDateTwo2  = $dateObj2->format('d/m/y');
        $shortDateFour2 = $dateObj2->format('d/m/Y');

        $dayWithDate     = $shortDateTwo2 . ' ' . $dayName2;
        $dayWithDateNew  = $shortDateFour2 . ' ' . $dayName2;
        $dayNameFirst    = $dayName2 . ' ' . $shortDateTwo2;
        $dayNameFirstNew = $dayName2 . ' ' . $shortDateFour2;

        $assignedRaw = WorkoutAssign::where('class_id', $classId)
            ->where(function ($q) use ($shortDateTwo2, $shortDateFour2, $dayWithDate, $dayWithDateNew, $dayNameFirst, $dayNameFirstNew) {
                $q->where('date', 'LIKE', '%' . $shortDateTwo2 . '%')
                    ->orWhere('date', 'LIKE', '%' . $shortDateFour2 . '%')
                    ->orWhere('date', 'LIKE', '%' . $dayWithDate . '%')
                    ->orWhere('date', 'LIKE', '%' . $dayWithDateNew . '%')
                    ->orWhere('date', 'LIKE', '%' . $dayNameFirst . '%')
                    ->orWhere('date', 'LIKE', '%' . $dayNameFirstNew . '%');
            })
            ->get();

        $assigned = $assignedRaw->groupBy('workout_type');
        $getIds = fn ($type) => isset($assigned[$type]) ? $assigned[$type]->pluck('workout_id')->toArray() : [];

        $detailstest = \App\Models\Test::whereIn('id', $getIds('test'))
            ->where('member_id', $member->id)
            ->with('workout.categoryOption')
            ->with('member')
            ->get();

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

        Log::info('Tests retrieved', [
            'count' => $detailstest->count(),
            'tests' => $detailstest->toArray()
        ]);

        $detailstest->map(function ($test) use ($dayWithDate) {
            if ($test->workout) {
                $typeValue = $test->workout->type;
                $typeRecord = \App\Models\Type::where('name', $typeValue)->first();

                $test->type_id = $typeRecord ? $typeRecord->id : null;
                $test->type_name = $typeValue;

                if ($test->type_id) {
                    $workoutManagers = \App\Models\WorkoutManager::where('type_id', $test->type_id)
                        ->where('date', $dayWithDate)
                        ->with('format')
                        ->get();

                    $test->workout_managers = $workoutManagers;
                }
            }
            return $test;
        });

        $categoryOptions = \App\Models\CategoryOption::select('id', 'category_name')->get();

        $workoutlibrary = WorkoutLibrary::with('categoryOption:id,category_name')
            ->get(['id', 'category_options_id', 'type', 'workout', 'link'])
            ->map(function ($item) {
                return [
                    'id'                   => $item->id,
                    'workout'              => $item->workout,
                    'type'                 => $item->type,
                    'category_option_id'   => $item->category_options_id,
                    'category_option_name' => $item->categoryOption->category_name ?? null,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Return tests grouped by workout type (from testsForDay)
        |--------------------------------------------------------------------------
        */
        $testStrength = [];
        $testWeightlifting = [];
        $testConditioning = [];

        if ($testsForDay->isNotEmpty()) {
            $libIds = $testsForDay->pluck('workout_libraries_id')->unique()->values();
            $libs = WorkoutLibrary::whereIn('id', $libIds)->get(['id', 'type']);

            $libTypeMap = [];
            foreach ($libs as $l) {
                $libTypeMap[$l->id] = strtolower(trim($l->type ?? ''));
            }

            foreach ($testsForDay as $t) {
                $tt = $libTypeMap[$t->workout_libraries_id] ?? '';
                if ($tt === 'strength') $testStrength[] = $t;
                elseif ($tt === 'weightlifting') $testWeightlifting[] = $t;
                elseif ($tt === 'conditioning') $testConditioning[] = $t;
            }
        }
        
        return response()->json([
            'status'            => true,
            'date'              => $dateString,
            'class_id'          => $classId,
            'workouts'          => $groupedWorkouts,
            'dayWithDate'       => $dayWithDate,
            'test'              => $detailstest,
            'workoutlibrary'    => $workoutlibrary,
            'categoryOptions'   => $categoryOptions,
            'member'            => $member,
            'score'             => $score,
            'test_data'         => [
                'strength'      => $testStrength, 
                'weightlifting' => $testWeightlifting, 
                'conditioning'  => $testConditioning
            ],
            'raw_tests_for_day' => $testsForDay,
        ], 200);


    } catch (\Exception $e) {
        Log::error('getWorkouts error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'status'  => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
