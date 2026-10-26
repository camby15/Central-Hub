<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/admin');
        }

        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function showInvite(string $token): View
    {
        return view('auth.invite', [
            'token' => $token,
            'email' => 'agent@example.com',
            'inviter' => 'Admin',
            'role' => 'Agent',
        ]);
    }

    public function acceptInvite(Request $request, string $token): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->firstOrCreate(
            ['email' => 'agent@example.com'],
            [
                'name' => $request->string('name')->toString(),
                'password' => $request->string('password')->toString(),
                'email_verified_at' => now(),
            ],
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/two-factor');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        return back()->with('status', 'If that email is on file, a reset link was sent. Links expire in 60 minutes.');
    }

    public function showResetPassword(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        return redirect()->route('login')->with('status', 'Your password was updated. Sign in to continue.');
    }

    public function showVerifyEmail(): View
    {
        return view('auth.verify-email');
    }

    public function showTwoFactor(): View
    {
        return view('auth.two-factor');
    }

    public function storeTwoFactor(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        return redirect('/admin')->with('status', 'Two-factor authentication is enabled.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
