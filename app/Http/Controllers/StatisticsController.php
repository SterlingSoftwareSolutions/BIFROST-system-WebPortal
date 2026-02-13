<?php

namespace App\Http\Controllers;

use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatisticsController extends Controller
{
    //
    public function viewstatistics()
    {
        $userId = Auth::id(); // Get the currently authenticated user's ID

         // Fetch the access record for the user
         $access = Access::where('user_id', $userId)->first();

         if ($access && $access->statistics === 'enable')
        {
             // Pass the access type to the view using compact
             $accessType = $access->access_type;
             return view('admin.user.statistics', compact('accessType'));
        }
        else
        {
             // Redirect to an unauthorized access view
             return view('error.unauthorized');
        }
    }
}
