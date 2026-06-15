<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(auth()->user()->categories ?? Category::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'type'  => 'required|in:income,expense',
            'color' => 'nullable|string|max:7',
        ]);
        $data['user_id'] = auth()->id();
        return response()->json(Category::create($data), 201);
    }

    public function update(Request $request, Category $category)
    {
        $category->update($request->only(['name', 'type', 'color']));
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
