<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! $this->passwordMatches($user, $credentials['password'])) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->onlyInput('email');
        }

        if (! $user->isActive()) {
            return back()
                ->withErrors(['email' => 'Your account is inactive. Please contact support.'])
                ->onlyInput('email');
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            $user->forceFill(['password' => Hash::make($credentials['password'])])->save();
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended($user->isAdmin() ? route('admin.dashboard') : route('home'))
            ->with('status', $user->isAdmin() ? 'Welcome Admin!' : 'Login successful!');
    }

    public function destroy(): RedirectResponse
    {
        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Logged out successfully.');
    }

    private function passwordMatches(User $user, string $password): bool
    {
        return Hash::check($password, $user->password) || hash_equals($user->password, $password);
    }
}
