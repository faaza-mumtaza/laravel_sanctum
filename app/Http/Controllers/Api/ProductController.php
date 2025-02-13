<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return ResponseHelper::success('List of products', $products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|string|in:draft,published,archived',
            'criteria' => 'required|string|in:perorangan,rombongan',
            'favorite' => 'boolean',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors()->first(), 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'status' => $request->status,
            'criteria' => $request->criteria,
            'favorite' => $request->favorite ?? false,
        ]);

        return ResponseHelper::success('Product created successfully', $product, 201);
    }

    public function show($id)
    {
        $product = Product::with('category')->withTrashed()->find($id);

        if (!$product) {
            return ResponseHelper::error('Product not found', 404);
        }

        return ResponseHelper::success('Product details', $product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::withTrashed()->find($id);

        if (!$product) {
            return ResponseHelper::error('Product not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|string|in:draft,published,archived',
            'criteria' => 'required|string|in:perorangan,rombongan',
            'favorite' => 'boolean',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error($validator->errors()->first(), 422);
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->update($request->only([
            'category_id', 'name', 'description', 'price', 'stock',
            'status', 'criteria', 'favorite'
        ]));

        return ResponseHelper::success('Product updated successfully', $product);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ResponseHelper::error('Product not found', 404);
        }

        $product->delete();
        return ResponseHelper::success('Product soft deleted successfully');
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->find($id);

        if (!$product) {
            return ResponseHelper::error('Product not found in trash', 404);
        }

        $product->restore();
        return ResponseHelper::success('Product restored successfully', $product);
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->find($id);

        if (!$product) {
            return ResponseHelper::error('Product not found in trash', 404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->forceDelete();
        return ResponseHelper::success('Product permanently deleted');
    }
}
