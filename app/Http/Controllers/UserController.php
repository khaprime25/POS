<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' => 'required|max:255',

            'email' => 'required|email|unique:users,email',

            'role' => 'required|in:cashier,chef',

            'password' => 'required|confirmed|min:6',

        ]);

        User::create([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'role' => $validated['role'],

            'password' => Hash::make($validated['password']),

            'is_active' => true,

        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function toggle(User $user)
    {
        if ($user->role === 'owner') {

            return back();
        }

        $user->update([

            'is_active' => !$user->is_active,

        ]);

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
