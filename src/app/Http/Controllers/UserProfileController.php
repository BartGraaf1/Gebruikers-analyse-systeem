<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // Make sure this line is added
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail; // Assuming you have created this Mailable

class UserProfileController extends Controller
{

    public function createProfile()
    {
        return view('user-profile');
    }

    public function storeProfile(Request $request)
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            'phone' => ['nullable', 'regex:/^(\+?[1-9]\d{1,14}|0\d{9})$/'],
            'location' => ['max:70'],
            'about_me' => ['max:150'],
        ]);

        $attribute = request()->validate([
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
        ]);


        User::where('id',Auth::user()->id)
            ->update([
                'name'    => $attributes['name'],
                'email' => $attribute['email'],
                'phone'     => $attributes['phone'],
                'location' => $attributes['location'],
                'about_me'    => $attributes["about_me"],
            ]);


        return redirect('dashboard')->with('success','Profile updated successfully');
    }
}
