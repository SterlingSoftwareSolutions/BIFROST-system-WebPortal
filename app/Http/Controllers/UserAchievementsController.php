<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAchievementsController extends Controller
{
    //
    public function viewachievement()
    {

        return view('user.user.achievements');
    }
}
