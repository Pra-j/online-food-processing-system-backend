<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Order::with('orderItems')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Not enough stock for {$product->name}");
                }
            }

            $order = Order::where('table_number', $validated['table_number'])
                ->where('status', '!=', 'completed')
                ->first();

            if (!$order) {
                // Create new order if none exists
                $order = Order::create([
                    'table_number' => $validated['table_number'],
                    'status' => 'queued',
                    'total_amount' => 0, // will calculate after adding items
                ]);
            }

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                $orderItem = OrderItem::where('order_id', $order->id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($orderItem) {
                    $orderItem->quantity += $item['quantity'];
                    $orderItem->total_price += $product->price * $item['quantity'];
                    $orderItem->save();
                } else {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'total_price' => $product->price * $item['quantity'],
                    ]);
                }

                $product->decrement('stock', $item['quantity']);

                $totalAmount += $product->price * $item['quantity'];
            }

            $order->total_amount += $totalAmount;
            $order->save();

            DB::commit();

            return response()->json([
                'message' => 'Order updated successfully',
                'order_id' => $order->id,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with([
            'orderItems.product:id,name,price,stock,category_id'
        ])->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json([
            'order_id' => $order->id,
            'table_number' => $order->table_number,
            'status' => $order->status,
            'total_amount' => $order->total_amount,
            'created_at' => $order->created_at->format('Y/m/d H:i'),
            'order_items' => $order->orderItems->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total_price' => $item->total_price,
                ];
            }),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

    }
}
