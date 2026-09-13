<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * DEFENSE: §5.3 customer register / login / profile
 * Board: "Phone validation?" → BD regex 01[3-9]
 */
class CustomerAuthController extends Controller
{
    // Show customer registration form.
    public function showRegister()
    {
        return view('website.customer.auth.register');
    }

    // Handle new customer registration and login.
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email:rfc,dns|max:255|unique:users,email',
            'phone' => 'required|string|regex:/^(\+?88)?01[3-9]\d{8}$/',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => 'customer',
            'is_active' => true,
            'password' => Hash::make($validated['password']),
        ]);

        $customer->syncLegacyRole();

        Auth::login($customer);
        $request->session()->regenerate();

        return redirect()->route('customer.account')->with('success', 'Welcome! Your customer account has been created.');
    }

    // Show customer login form.
    public function showLogin()
    {
        return view('website.customer.auth.login');
    }

    // Authenticate a customer user.
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => 'customer',
            'is_active' => 1,
        ], $request->boolean('remember'))) {
            $user = Auth::user();
            if ($user instanceof User) {
                $user->syncLegacyRole();
            }
            $request->session()->regenerate();
            return redirect()->route('customer.account');
        }

        return back()->withErrors([
            'email' => 'Invalid customer credentials or inactive account.',
        ])->onlyInput('email');
    }

    // Display the authenticated customer's account page.
    public function account()
    {
        return view('website.customer.account');
    }

    // Update basic profile fields for the customer.
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return redirect()->route('customer.account')->with('success', 'Account updated successfully.');
    }
}
