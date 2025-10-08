<?php

namespace App\Http\Controllers;

use App\Models\MonthlyImage;
use App\Models\Newprofile;
use Carbon\Carbon;
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
        ->where(function ($query) use ($months) {
            foreach ($months as $month) {
                $query->orWhere(function ($query) use ($month) {
                    $query->whereMonth('month', $month['month'])
                        ->whereYear('month', $month['year']);
                });
            }
        })
        ->get()
        ->groupBy(function ($item) {
            return Carbon::parse($item->month)->format('Y-m'); // Group by year-month
        });

    // Convert grouped images into a clean JSON-friendly format
    $formattedImages = $images->map(function ($group) {
        return $group->map(function ($item) {
            return [
                'id' => $item->id,
                'image_path' => asset('storage/' . $item->image_path),
                'month' => Carbon::parse($item->month)->format('F Y'),
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

}
