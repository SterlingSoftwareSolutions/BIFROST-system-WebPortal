<?php

namespace App\Http\Controllers;

use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\FuncCall;

class DashboardController extends Controller
{
     public function viewDashboard()
     {
        $userId = Auth::id(); // Get the currently authenticated user's ID

         // Fetch the access record for the user
         $access = Access::where('user_id', $userId)->first();
         $members = \App\Models\Newprofile::all();

         // Calculate counts for Dashboard Blocks
         $activeMembersCount = \App\Models\Newprofile::where('is_subsactive', 1)->count();
         $inactiveMembersCount = \App\Models\Newprofile::where('is_subsactive', 0)->count();

         // Logic for Members Not Trained in Over 7 Days
         $cutoffDate = \Carbon\Carbon::now()->subDays(7);
         $notTrainedMembers = \App\Models\Newprofile::whereHas('user', function ($q) use ($cutoffDate) {
             $q->whereDoesntHave('scores', function ($sq) use ($cutoffDate) {
                 $sq->where('created_at', '>=', $cutoffDate);
             });
         })->get();
         
         $notTrainedCount = $notTrainedMembers->count();

         // Estimated Revenue Logic (Mockup based on subscription_level)
         $estimatedRevenue = 0;
         foreach ($members as $member) {
             if ($member->is_subsactive) {
                 switch ($member->subscription_level) {
                     case 'Unlimited':
                         $estimatedRevenue += 150;
                         break;
                     case '10 Pack':
                         $estimatedRevenue += 100;
                         break;
                     case 'Online':
                         $estimatedRevenue += 50;
                         break;
                     default:
                         $estimatedRevenue += 50;
                         break;
                 }
             }
         }

         if ($access && $access->dashboard === 'enable')
        {
             // Pass the access type to the view using compact
             $accessType = $access->access_type;
             return view('admin.user.dashboard', compact('accessType', 'members', 'activeMembersCount', 'inactiveMembersCount', 'notTrainedMembers', 'notTrainedCount', 'estimatedRevenue'));
        }
        else
        {
             // Redirect to an unauthorized access view
             return view('error.unauthorized');
        }
     }
}
