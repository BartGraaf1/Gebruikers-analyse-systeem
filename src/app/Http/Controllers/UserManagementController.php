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

class UserManagementController extends Controller
{

    public function create()
    {
        // Displays the form to create a new user
        return view('user-management/user-add');
    }

    public function store(Request $request)
    {
        // Validates and stores a new user
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => ['nullable', 'regex:/^(\+?[1-9]\d{1,14}|0\d{9})$/'],
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


        return redirect('/user-management')->with('success', 'User was successfully added.');
    }

    public function index()
    {
        // Retrieves all user
        $users = User::all();
        return view('user-management/users-overview', compact('users'));
    }

    public function edit(User $user)
    {
        // Displays the form to edit an existing user
        return view('user-management/user-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Capture old role
        $oldRole = $user->user_role;

        // Validates and updates the user
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'regex:/^(\+?[1-9]\d{1,14}|0\d{9})$/'],
            'location' => 'nullable|max:255',
            'user_role' => 'required|max:1', // Ensure role is always provided and checked
        ]);

        $user->update($validatedData);

        // Check if the role has changed from inactive (0) to active (not 0)
        if ($oldRole == '0' && $validatedData['user_role'] != '0') {
            // Notify user of role change
            $user->notify(new \App\Notifications\UserRoleChangedNotification($user));
        }

        return redirect('/user-management')->with('success', 'User successfully updated.');
    }


    public function confirmDelete(User $user)
    {
        // Assuming 'User' is your model and it's correctly type-hinted to resolve the user instance
        return view('user-management/user-delete', compact('user'));
    }


    public function destroy(User $user)
    {
        // Deletes the user
        $user->delete();

        return redirect('/user-management')->with('success', 'User successfully deleted.');
    }
}
