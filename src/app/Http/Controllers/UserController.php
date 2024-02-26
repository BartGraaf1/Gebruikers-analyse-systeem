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

class UserController extends Controller
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
            'phone'     => ['max:50'],
            'location' => ['max:70'],
            'about_me'    => ['max:150'],
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


    public function create()
    {
        // Displays the form to create a new user
        return view('user/user-add');
    }

    public function store(Request $request)
    {


        // Validates and stores a new user
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'nullable|max:50',
            'location' => 'nullable|max:255',
            'about_me' => 'nullable|max:255',
            'user_role' => 'max:1',
        ]);

        // Generate a random password
        $password = Str::random(10); // Adjust length as needed

        // Add the hashed password to the validated data
        $validatedData['password'] = Hash::make($password);

        // Create the user
        $user = User::create($validatedData);

        // Send an email with the password
        $user->notify(new \App\Notifications\WelcomeEmail($user, $password));


        return redirect('/users')->with('success', 'User was successfully added.');
    }

    public function index()
    {
        // Retrieves all user
        $users = User::all();
        return view('user/users-overview', compact('users'));
    }

    public function edit(User $user)
    {
        // Displays the form to edit an existing user
        return view('user/user-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Validates and updates the user
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|max:50',
            'location' => 'nullable|max:255',
            'user_role' => 'max:1',
        ]);

        $user->update($validatedData);

        return redirect('/users')->with('success', 'User successfully updated.');
    }

    public function confirmDelete(User $user)
    {
        // Assuming 'User' is your model and it's correctly type-hinted to resolve the user instance
        return view('user/user-delete', compact('user'));
    }


    public function destroy(User $user)
    {
        // Deletes the user
        $user->delete();

        return redirect('/users')->with('success', 'User successfully deleted.');
    }
}
