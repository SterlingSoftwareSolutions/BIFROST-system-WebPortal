<?php

namespace App\Http\Controllers;

use App\Models\DailyStrength;
use App\Models\MonthlyImage;
use App\Models\Newprofile;
use App\Models\WorkoutLibrary;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
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

        // Decode the image paths if available
        $imagePaths = json_decode($member->image_paths ?? '[]', true);

        // Determine profile image
        $profileImage = (!empty($imagePaths) && is_array($imagePaths))
            ? asset('storage/' . $imagePaths[0])
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
            'profileImage' => $profileImage,
            'months' => $months,
            'images' => $formattedImages,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|date',
            'front_image' => 'required|image|mimes:png,jpg,jpeg,gif,img',
            'side_image' => 'required|image|mimes:png,jpg,jpeg,gif,img',
            'back_image' => 'required|image|mimes:png,jpg,jpeg,gif,img',
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            // Store uploaded images in 'public/images'
            $frontImagePath = $request->file('front_image')->store('images', 'public');
            $sideImagePath = $request->file('side_image')->store('images', 'public');
            $backImagePath = $request->file('back_image')->store('images', 'public');

            // Save to database
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

        } catch (Exception $e) {
            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload images.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //get strength type workouts
    public function getStrengthWorkouts()
    {
        try {
            // Fetch all workouts where type = 'strength'
            $workouts = WorkoutLibrary::where('type', 'strength')->get();

            if ($workouts->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No strength workouts found.',
                    'data' => [],
                ], 200);
            }

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Strength workouts retrieved successfully.',
                'count' => $workouts->count(),
                'data' => $workouts,
            ], 200);

        } catch (Exception $e) {
            // Handle any unexpected errors
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch strength workouts.',
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
            'strength_id' => 'required|exists:workout_libraries,id',
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

            // Fetch all daily strength records for this member and strength ID
            $strengthData = DailyStrength::where('member_id', $member->id)
                ->where('strength_id', $request->strength_id)
                ->orderBy('date', 'asc')
                ->get();

            if ($strengthData->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No strength data found for this workout.',
                    'data' => []
                ], 200);
            }

            // Prepare data for graph
            $graphData = $strengthData->map(function ($item) {
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
            });

            // Calculate summary metrics
            $totalReps = $strengthData->sum('reps');
            $totalWeight = $strengthData->sum('weight');
            $totalSets = $strengthData->count();
            $oneRepMax = $strengthData->max(function ($item) {
                return $item->weight * (1 + ($item->reps / 30)); // Epley formula
            });

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Strength progress retrieved successfully.',
                'data' => [
                    'strength_id' => (int) $request->strength_id,
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
                'message' => 'Failed to fetch strength data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
