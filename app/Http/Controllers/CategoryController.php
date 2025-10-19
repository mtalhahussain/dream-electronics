<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        // Simple permission check - admin users bypass all permission checks
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            
            // Admin users have full access
            if ($user && $user->hasRole('admin')) {
                return $next($request);
            }
            
            // For non-admin users, check specific permissions
            $action = $request->route()->getActionMethod();
            switch ($action) {
                case 'index':
                case 'show':
                    if (!$user || !$user->can('view-categories')) {
                        abort(403, 'Unauthorized');
                    }
                    break;
                case 'store':
                    if (!$user || !$user->can('create-categories')) {
                        abort(403, 'Unauthorized');
                    }
                    break;
                case 'update':
                    if (!$user || !$user->can('edit-categories')) {
                        abort(403, 'Unauthorized');
                    }
                    break;
                case 'destroy':
                    if (!$user || !$user->can('delete-categories')) {
                        abort(403, 'Unauthorized');
                    }
                    break;
            }
            
            return $next($request);
        });
    }

    public function index()
    {
        $categories = Category::withCount('products')->ordered()->paginate(15);
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        try {
            $data = $request->all();
            $data['slug'] = Str::slug($request->name);
            $data['is_active'] = $request->boolean('is_active', true);
            $data['sort_order'] = $request->input('sort_order', 0);

            $category = Category::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        try {
            $data = $request->all();
            $data['slug'] = Str::slug($request->name);
            $data['is_active'] = $request->boolean('is_active', true);
            $data['sort_order'] = $request->input('sort_order', $category->sort_order);

            $category->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!',
                'category' => $category->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, Category $category): JsonResponse
    {
        try {
            // Check if category has products
            if ($category->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category. It has associated products.'
                ], 422);
            }

            $categoryName = $category->name;
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => "Category '{$categoryName}' deleted successfully!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    // API endpoint for getting active categories (for dropdowns)
    public function getActive(): JsonResponse
    {
        try {
            $categories = Category::active()->ordered()->get(['id', 'name', 'color', 'icon']);
            
            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories'
            ], 500);
        }
    }
}
