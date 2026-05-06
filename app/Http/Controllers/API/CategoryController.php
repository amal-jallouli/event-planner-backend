<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::withCount('events')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:categories']);
        $category = Category::create($request->only('name'));
        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        $category->loadCount('events');
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);
        $category->update($request->only('name'));
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        if ($category->events()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete a category that has associated events.'
            ], 422);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
