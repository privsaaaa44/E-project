<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $request->session()->put('reset_email', strtolower(trim($request->validated('email'))));

        return redirect()->route('password.reset')->with('status', 'Email confirmed. Enter your new password.');
    }

    public function edit(): View|RedirectResponse
    {
        if (! session()->has('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', [
            'email' => session('reset_email'),
        ]);
    }

    public function update(ResetPasswordRequest $request): RedirectResponse
    {
        $email = $request->session()->get('reset_email');

        if (! $email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Please confirm your email first.',
            ]);
        }

        User::where('email', $email)->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        $request->session()->forget('reset_email');

        return redirect()->route('login')->with('status', 'Password updated successfully.');
    }
}
