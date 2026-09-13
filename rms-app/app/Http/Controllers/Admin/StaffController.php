<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * DEFENSE: admin-only staff CRUD (manager / chef / cashier users)
 */
class StaffController extends Controller
{
    // List staff members with pagination.
    public function index()
    {
        $staff = User::whereIn('role', ['manager', 'chef', 'cashier'])->latest()->paginate(15);
        return view('admin.staff.index', compact('staff'));
    }

    // Validate and create a staff user.
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email:rfc,dns|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:manager,chef,cashier',
            'phone' => 'required|string|regex:/^(\+?88)?01[3-9]\d{8}$/',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => true,
        ])->syncLegacyRole();
        return redirect()->route('staff.index')->with('success', 'Staff member added!');
    }

    // Validate and update staff details.
    public function update(Request $request, User $staff)
    {
        if (!in_array($staff->role, ['manager', 'chef', 'cashier'], true)) {
            return redirect()->route('staff.index')->with('error', 'Only staff members can be edited from this page.');
        }

        $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email:rfc,dns|max:255|unique:users,email,' . $staff->id,
            'role' => 'required|in:manager,chef,cashier',
            'phone' => 'required|string|regex:/^(\+?88)?01[3-9]\d{8}$/',
        ]);
        $data = $request->only('name', 'email', 'role', 'phone');
        $data['is_active'] = $request->has('is_active');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $staff->update($data);
        $staff->syncLegacyRole();
        return redirect()->route('staff.index')->with('success', 'Staff member updated!');
    }

    // Remove a staff user from the system.
    public function destroy(User $staff)
    {
        if (!in_array($staff->role, ['manager', 'chef', 'cashier'], true)) {
            return redirect()->route('staff.index')->with('error', 'Only staff members can be removed from this page.');
        }

        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff member removed!');
    }
}
