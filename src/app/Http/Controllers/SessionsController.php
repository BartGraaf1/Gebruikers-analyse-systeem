<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if (Auth::attempt($attributes)) {
            // Authentication passed...
            // Check if the user's role is approved
            if (Auth::user()->user_role == '0') {
                Auth::logout();  // Important: Logout the user immediately
                return back()->withErrors(['email' => 'Your account has not been approved.']);
            }

            session()->regenerate();
            return redirect('dashboard')->with('success', 'You are logged in.');
        } else {
            return back()->withErrors(['email' => 'Email or password invalid.']);
        }
    }

    public function destroy()
    {

        Auth::logout();
        return redirect('/login')->with(['success'=>'You\'ve been logged out.']);
    }
}
