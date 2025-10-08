<?php

namespace App\Http\Controllers;

use App\Models\MonthlyImage;
use App\Models\Newprofile;
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

}
