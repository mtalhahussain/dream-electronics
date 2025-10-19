<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-branches')->only(['index']);
        $this->middleware('can:create-branches')->only(['store']);
        $this->middleware('can:edit-branches')->only(['update']);
        $this->middleware('can:delete-branches')->only(['destroy']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $branches = Branch::where('is_active', true)->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'branches' => $branches
            ]);
        }
        
        $branches = Branch::orderBy('name')->paginate(15);
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request): JsonResponse
    {
        
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
            'location' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'manager_name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        try {

            $branch = Branch::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Branch created successfully!',
                'branch' => $branch
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create branch: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Branch $branch): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
            'location' => 'required|string|max:255',
            'manager_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_active' => 'boolean'
        ]);

        try {
            $branch->update([
                'name' => $request->name,
                'location' => $request->location,
                'manager_name' => $request->manager_name,
                'phone' => $request->phone,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Branch updated successfully!',
                'branch' => $branch
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update branch: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Branch $branch): JsonResponse
    {
        try {
            // Check if branch has any related data that would prevent deletion
            $hasRelatedData = $branch->sales()->exists() || 
                             $branch->employees()->exists() || 
                             $branch->expenses()->exists() || 
                             $branch->financeTransactions()->exists();

            if ($hasRelatedData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete branch. It has related sales, employees, expenses, or finance transactions.'
                ], 422);
            }

            $branchName = $branch->name;
            $branch->delete();

            return response()->json([
                'success' => true,
                'message' => "Branch '{$branchName}' deleted successfully!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete branch: ' . $e->getMessage()
            ], 500);
        }
    }
}
