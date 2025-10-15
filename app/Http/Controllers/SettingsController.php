<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-settings')->only(['index', 'show']);
        $this->middleware('can:manage-settings')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $branches = Branch::orderBy('name')->get();
        return view('settings.index', compact('branches'));
    }

    public function create()
    {
        return view('settings.create');
    }

    public function store(Request $request)
    {
        // Implementation for store method
        return redirect()->route('settings.index')
            ->with('success', 'Settings saved successfully');
    }

    public function show($id)
    {
        return view('settings.show');
    }

    public function edit($id)
    {
        return view('settings.edit');
    }

    public function update(Request $request, $id)
    {
        // Implementation for update method
        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully');
    }

    public function destroy($id)
    {
        return redirect()->route('settings.index')
            ->with('success', 'Setting deleted successfully');
    }
}