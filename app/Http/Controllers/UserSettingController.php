<?php

namespace App\Http\Controllers;

use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UserSettingController extends Controller
{
    //
    public function viewsetting()
    {
        return view('user.user.settings');
    }
}
