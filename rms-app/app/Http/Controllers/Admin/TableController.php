<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

/**
 * DEFENSE: §5.10 dining tables CRUD + occupy/free status
 */
class TableController extends Controller
{
    // Display all tables with their active orders.
    public function index()
    {
        $tables = Table::with('activeOrder')->get();
        return view('admin.tables.index', compact('tables'));
    }

    // Validate and create a new table.
    public function store(Request $request)
    {
        $request->validate(['table_number' => 'required|string|max:255|unique:tables', 'capacity' => 'required|integer|min:1', 'location' => 'nullable|string|max:255']);
        Table::create($request->only('table_number', 'capacity', 'location'));
        return redirect()->route('tables.index')->with('success', 'Table added successfully!');
    }

    // Validate and update a table.
    public function update(Request $request, Table $table)
    {
        $request->validate(['table_number' => 'required|string|max:255|unique:tables,table_number,' . $table->id, 'capacity' => 'required|integer|min:1', 'status' => 'required|in:available,occupied,reserved,maintenance', 'location' => 'nullable|string|max:255']);
        $table->update($request->only('table_number', 'capacity', 'status', 'location'));
        return redirect()->route('tables.index')->with('success', 'Table updated successfully!');
    }

    // Delete a table record.
    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('tables.index')->with('success', 'Table deleted successfully!');
    }

    // Update only the status field for a table.
    public function updateStatus(Request $request, Table $table)
    {
        $request->validate(['status' => 'required|in:available,occupied,reserved,maintenance']);
        $table->update(['status' => $request->status]);
        return redirect()->route('tables.index')->with('success', 'Table status updated!');
    }
}
