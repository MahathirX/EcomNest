<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],//
            'role' => ['required', 'string', 'in:admin,manager,user'], //
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user', // Default role is 'user'
            
        ]);

        event(new Registered($user));

        Auth::login($user);

        // // Redirect based on role
        // if ($user->role === 'admin') {
        //     return redirect()->route('admin.dashboard');
        // } elseif ($user->role === 'manager') {
        //     return redirect()->route('manager.dashboard');
        // } else {
        //     return redirect()->route('user.dashboard');
        // }
        

        return redirect()->route('verification.notice')->with('success', 'আপনার অ্যাকাউন্ট তৈরি হয়েছে, দয়া করে ইমেইল ভেরিফাই করুন।');
    }
}