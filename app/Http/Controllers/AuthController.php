<?php

// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRoleSelection()
    {
        return view('auth.choose-role');
    }

    public function showLoginForm($role)
    {
        if (!in_array($role, ['student', 'supervisor', 'admin'])) {
            return redirect('/');
        }

        return view('auth.login', compact('role'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:student,supervisor,admin',
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->role !== $request->role) {
                Auth::logout();
                return back()->withErrors([
                    'role' => 'Vous n\'avez pas les autorisations pour vous connecter en tant que ' . $request->role,
                ]);
            }

            $request->session()->regenerate();

            switch ($user->role) {
                case 'student':
                    return redirect()->route('student.dashboard');
                case 'supervisor':
                    return redirect()->route('supervisor.dashboard');
                case 'admin':
                    return redirect()->route('admin.dashboard');
                default:
                    return redirect('/');
            }
        }

        return back()->withErrors([
            'email' => 'Les informations de connexion ne correspondent pas à nos enregistrements.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }
}
