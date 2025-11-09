<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::with('offers', 'category')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'price' => 'required|integer',
            'category_id' => 'required|integer'
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'category' => $product,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Product::with('category', 'offers')->where('id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'integer',
            'category_id' => 'integer',
            'stock' => 'integer'

        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'product updated successfully',
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'product deleted successfully'
        ], 200);
    }

    public function productsStatus(string $id, $startDate = null, $endDate = null)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $query = $product->orderItems()->with('order');

        if ($startDate && $endDate) {
            $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        $orderItems = $query->get();

        $totalSoldQuantity = $orderItems->sum('quantity');
        $totalRevenue = $orderItems->sum('total_price');
        $totalOrders = $orderItems->count();
        $averagePrice = $totalSoldQuantity > 0 ? $totalRevenue / $totalSoldQuantity : 0;

        $firstSoldAt = $orderItems->min('created_at');
        $lastSoldAt = $orderItems->max('created_at');

        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'stock_remaining' => $product->stock,
            'total_quantity_sold' => $totalSoldQuantity,
            'total_revenue' => $totalRevenue,
            'average_price_sold' => round($averagePrice, 2),
            'total_orders' => $totalOrders,
            'first_sold_at' => $firstSoldAt ? $firstSoldAt->format('Y/m/d H:i') : null,
            'last_sold_at' => $lastSoldAt ? $lastSoldAt->format('Y/m/d H:i') : null,
        ]);
    }
}
