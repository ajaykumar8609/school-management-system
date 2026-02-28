<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $email = $request->email;
        if (!str_contains($email, '@')) {
            $email = $email . '@school.com';
        }

        if (Auth::attempt(['email' => $email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Auto-create admin if no users exist (for first deploy without Shell)
        if (User::count() === 0 && $email === 'admin@school.com' && $request->password === 'password') {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@school.com',
                'password' => 'password',
            ]);
            if (Auth::attempt(['email' => $email, 'password' => $request->password], $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'));
            }
        }

        return back()->withErrors(['email' => 'Invalid admin or password.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
