<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

/**
 * CustomerController
 *
 * Admin tools for managing customer accounts.
 */
class CustomerController extends Controller
{
    // List customers with pagination.
    public function index()
    {
        $customers = User::where('role', 'customer')->latest()->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    // Delete a customer account.
    public function destroy(User $customer)
    {
        if ($customer->role !== 'customer') {
            return redirect()->route('customers.index')->with('error', 'Only customer accounts can be removed from this page.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer removed!');
    }

    // Toggle active/inactive status for a customer.
    public function toggleStatus(User $customer)
    {
        if ($customer->role !== 'customer') {
            return redirect()->route('customers.index')->with('error', 'Only customer accounts can be updated from this page.');
        }

        $customer->update(['is_active' => ! $customer->is_active]);

        $message = $customer->is_active ? 'Customer activated.' : 'Customer set to inactive.';

        return redirect()->route('customers.index')->with('success', $message);
    }
}
