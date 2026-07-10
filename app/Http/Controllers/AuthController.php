<?php

namespace App\Http\Controllers;

use App\Models\LoginActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $remember = $request->boolean('remember');
        $ok = Auth::attempt($credentials + ['is_active' => true], $remember);

        LoginActivity::create([
            'user_id' => Auth::id(),
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'successful' => $ok,
            'logged_at' => now(),
        ]);

        if (! $ok) {
            return back()->withErrors(['email' => 'Login gagal atau user nonaktif.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->user()->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create($data);
        $user->assignRole('Karyawan');
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function forgotForm()
    {
        return view('auth.forgot');
    }

    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Link reset password dikirim jika email terdaftar.');
    }

    public function resetForm(string $token)
    {
        return view('auth.reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset($data, function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        $google = Socialite::driver('google')->user();
        $user = User::firstOrCreate(
            ['email' => $google->getEmail()],
            ['name' => $google->getName() ?: $google->getNickname(), 'password' => Hash::make(str()->password(32)), 'is_active' => true]
        );
        $user->update(['google_id' => $google->getId(), 'last_login_at' => now()]);
        $user->assignRole($user->roles()->exists() ? $user->roles->pluck('name')->all() : ['Karyawan']);
        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        activity('auth')->causedBy($request->user())->log('Logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
