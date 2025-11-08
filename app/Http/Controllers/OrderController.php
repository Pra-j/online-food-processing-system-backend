<?php

namespace App\Http\Controllers;

use App\Models\Offer;
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
                $order = Order::create([
                    'table_number' => $validated['table_number'],
                    'status' => 'queued',
                    'total_amount' => 0,
                ]);
            }

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $originalPrice = $product->price * $item['quantity'];
                $discountedPrice = 0;
                $freebies = 0;

                $offer = Offer::where('type', 'product')
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();


                if ($offer) {
                    switch ($offer->offer_kind) {
                        case 'percentage':
                            $discountedPrice += ($product->price * ($offer->value / 100)) * $item['quantity'];
                            break;

                        case 'fixed_amount':
                            $discountedPrice += ($offer->value) * $item['quantity'];
                            if ($discountedPrice < 0) $discountedPrice = 0;
                            break;

                        case 'buy_x_get_y':
                            if ($item['quantity'] >= $offer->buy_quantity) {
                                $freebies = $offer->get_quantity;
                            }
                            break;
                    }

                    $offer->num_used += $item['quantity'];
                    if ($offer->num_used >= $offer->max_usage) {
                        $offer->is_active = false;
                    }
                    $offer->save();
                }


                $orderItem = OrderItem::where('order_id', $order->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($orderItem) {
                    $orderItem->quantity += $item['quantity'];
                    $orderItem->total_product_price += $originalPrice;
                    $orderItem->discount += $discountedPrice;
                    $orderItem->grand_total += $originalPrice - $discountedPrice;
                    $orderItem->save();
                } else {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'total_product_price' => $originalPrice,
                        'discount' => $discountedPrice,
                        'grand_total' => $originalPrice - $discountedPrice,
                        'offer_id' => $offer->id
                    ]);
                }

                $product->decrement('stock', $item['quantity']);
                if ($freebies) {
                    $product->decrement('stock', $freebies);
                }

                $totalAmount += $originalPrice - $discountedPrice;
            }

            $globalOffer = Offer::where('type', 'global')
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            $globalDiscount = 0;
            if ($globalOffer) {
                if ($globalOffer->offer_kind === 'percentage') {
                    $globalDiscount = $totalAmount * ($globalOffer->value / 100);
                } elseif ($globalOffer->offer_kind === 'fixed_amount') {
                    $globalDiscount = $globalOffer->value;
                }

                $totalAmount = max($totalAmount - $globalDiscount, 0);

                $globalOffer->num_used++;
                if ($globalOffer->num_used >= $globalOffer->max_usage) {
                    $globalOffer->is_active = false;
                }
                $globalOffer->save();
            }

            $order->total_amount += $totalAmount;
            $order->save();

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'global_discount' => $globalDiscount,
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
                    'total_product_price' => $item->total_product_price,
                    'discount' => $item->discount,
                    'grand_total' => $item->grand_total
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
