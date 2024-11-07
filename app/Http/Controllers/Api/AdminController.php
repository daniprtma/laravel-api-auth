<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function showUsers(Request $request)
    {
        $users = User::all();

        return view('admin.index', compact($users));
    }
}
