<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = \App\Models\Position::latest()->get();
        return \Inertia\Inertia::render('Admin/Positions/Index', [
            'positions' => $positions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        \App\Models\Position::create($validated);

        return redirect()->back()->with('success', 'Posisi berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $position = \App\Models\Position::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $position->update($validated);

        return redirect()->back()->with('success', 'Posisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $position = \App\Models\Position::findOrFail($id);
        $position->delete();

        return redirect()->back()->with('success', 'Posisi berhasil dihapus.');
    }
}
