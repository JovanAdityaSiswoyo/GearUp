<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->paginate(5);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Map 'name' to 'categories' field for database
        $category = Category::create([
            'categories' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Log activity
        ActivityLog::create([
            'log_name' => 'category',
            'description' => 'Created category: ' . $category->categories,
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'created',
            'properties' => json_encode(['category_name' => $category->categories]),
        ]);

        alert()->success('Success', 'Category created successfully!');
        return redirect()->route('admin.categories.index');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Map 'name' to 'categories' field for database
        $category->update([
            'categories' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Log activity
        ActivityLog::create([
            'log_name' => 'category',
            'description' => 'Updated category: ' . $category->categories,
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'updated',
            'properties' => json_encode(['category_name' => $category->categories]),
        ]);

        alert()->success('Success', 'Category updated successfully!');
        return redirect()->route('admin.categories.index');
    }

    public function destroy(Category $category)
    {
        $categoryName = $category->categories;
        
        $category->delete();
        
        // Log activity
        ActivityLog::create([
            'log_name' => 'category',
            'description' => 'Deleted category: ' . $categoryName,
            'subject_type' => Category::class,
            'subject_id' => null,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'deleted',
            'properties' => json_encode(['category_name' => $categoryName]),
        ]);
        
        alert()->success('Deleted', 'Category deleted successfully!');
        return redirect()->route('admin.categories.index');
    }
}
