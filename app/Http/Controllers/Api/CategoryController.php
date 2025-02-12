<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return ResponseHelper::success('List of categories', $categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors()->first(), 422);
        }

        $category = Category::create($request->only('name', 'description'));
        return ResponseHelper::success('Category created successfully', $category, 201);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return ResponseHelper::error('Category not found', 404);
        }

        return ResponseHelper::success('Category details', $category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return ResponseHelper::error('Category not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors()->first(), 422);
        }

        $category->update($request->only('name', 'description'));
        return ResponseHelper::success('Category updated successfully', $category);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return ResponseHelper::error('Category not found', 404);
        }

        $category->delete();
        return ResponseHelper::success('Category deleted successfully');
    }
}
