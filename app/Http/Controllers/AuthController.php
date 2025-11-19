<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cari user by username
        $user = User::where('username', $credentials['username'])->first();

        // Password di database menggunakan MD5 (sesuai SQL dump)
        if ($user && md5($credentials['password']) === $user->password) {
            Auth::login($user);
            
            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Admin ' . Auth::user()->name . ' berhasil login'
            ]);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Admin ' . Auth::user()->name . ' logout'
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}