<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = Order::with('cashier')->get();
            return ResponseHelper::success('List of orders', $orders);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to retrieve orders: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'transaction_time' => 'required|date',
                'total_price' => 'required|integer|min:0',
                'total_item' => 'required|integer|min:1',
                'payment_amount' => 'required|integer|min:0',
                'cashier_id' => 'required|exists:users,id',
                'cashier_name' => 'required|string|max:255',
                'payment_method' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::error($validator->errors()->first(), 422);
            }

            $order = Order::create([
                'transaction_time' => Carbon::parse($request->transaction_time),
                'total_price' => $request->total_price,
                'total_item' => $request->total_item,
                'payment_amount' => $request->payment_amount,
                'cashier_id' => $request->cashier_id,
                'cashier_name' => $request->cashier_name,
                'payment_method' => $request->payment_method,
            ]);

            return ResponseHelper::success('Order created successfully', $order, 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $order = Order::with('cashier')->find($id);

            if (!$order) {
                return ResponseHelper::error('Order not found', 404);
            }

            return ResponseHelper::success('Order details', $order);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to retrieve order: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return ResponseHelper::error('Order not found', 404);
            }

            $validator = Validator::make($request->all(), [
                'total_price' => 'required|integer|min:0',
                'total_item' => 'required|integer|min:1',
                'payment_amount' => 'required|integer|min:0',
                'payment_method' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::error($validator->errors()->first(), 422);
            }

            $order->update($request->only([
                'total_price', 'total_item', 'payment_amount', 'payment_method'
            ]));

            return ResponseHelper::success('Order updated successfully', $order);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update order: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return ResponseHelper::error('Order not found', 404);
            }

            $order->delete();
            return ResponseHelper::success('Order deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to delete order: ' . $e->getMessage(), 500);
        }
    }
}
