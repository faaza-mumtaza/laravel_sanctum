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
        try {
            $products = Product::with('category')->get();
            return ResponseHelper::success('List of products', $products);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
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

            $imagePath = $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null;

            $product = Product::create(array_merge($request->all(), ['image' => $imagePath]));

            return ResponseHelper::success('Product created successfully', $product, 201);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with('category')->withTrashed()->find($id);
            if (!$product) {
                return ResponseHelper::error('Product not found', 404);
            }
            return ResponseHelper::success('Product details', $product);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
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

            $product->update($request->except(['image']));

            return ResponseHelper::success('Product updated successfully', $product);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return ResponseHelper::error('Product not found', 404);
            }

            $product->delete();
            return ResponseHelper::success('Product soft deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function restore($id)
    {
        try {
            $product = Product::onlyTrashed()->find($id);
            if (!$product) {
                return ResponseHelper::error('Product not found in trash', 404);
            }

            $product->restore();
            return ResponseHelper::success('Product restored successfully', $product);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $product = Product::onlyTrashed()->find($id);
            if (!$product) {
                return ResponseHelper::error('Product not found in trash', 404);
            }

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->forceDelete();
            return ResponseHelper::success('Product permanently deleted');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }
}
