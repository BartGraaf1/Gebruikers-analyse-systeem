<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminNewUserNotification;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create()
    {
        return view('session.register');
    }

    public function store()
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'max:20']
        ]);

        $attributes['password'] = bcrypt($attributes['password']);
        $attributes['user_role'] = 0;  // Default role

        $user = User::create($attributes);

        // Fetch all admins
        $admins = User::where('user_role', '1')->get();
        foreach ($admins as $admin) {
            $admin->notify(new AdminNewUserNotification($user));
        }

        session()->flash('success', 'You have requested for your account to have access to the application. Please wait for approval.');
        return redirect('/login');
    }
}
