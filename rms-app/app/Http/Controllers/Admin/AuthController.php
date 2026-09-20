<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * DEFENSE Q5/Q18: Staff Admin Panel login.
 * Rejects role=customer (they must use customer login).
 * Rejects inactive users. Regenerates session after Auth::login.
 */
class AuthController extends Controller
{
    // Show the staff/admin login form.
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    /**
     * DEFENSE Q5: Staff auth — customer role bounced with clear error message.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'No account found for this email address.',
            ])->onlyInput('email');
        }

        if ($user->role === 'customer') {
            return back()->withErrors([
                'email' => 'This is the staff login. Customers should use the customer login page.',
            ])->onlyInput('email');
        }

        if (! $user->is_active) {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact an administrator.',
            ])->onlyInput('email');
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'The password you entered is incorrect.',
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));

        if ($user instanceof User) {
            $user->syncLegacyRole();
        }

        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    // Logout current user and regenerate session.
    public function logout(Request $request)
    {
        $currentRole = auth()->user()?->role;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($currentRole === 'customer') {
            return redirect()->route('website.home');
        }

        return redirect('/login');
    }
}
