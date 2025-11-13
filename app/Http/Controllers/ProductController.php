<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::with('offers', 'category')
            ->where('stock', '!=', 0)
            ->orderBy('id', 'desc')
            ->get();
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
            'category_id' => 'required|integer',
            'stock' => 'required|integer',
            'food_type' => 'required|in:veg,non-veg,drinks',
            'course_type' => 'required|in:appetizer,main,dessert'
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
            'stock' => 'integer',
            'food_type' => 'in:veg,non-veg,drinks',

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

    public function productSalesSummary(Request $request)
    {
        $dateFrom = Carbon::parse($request->input('date_from', now()->toDateString()))->startOfDay();
        $dateTo = Carbon::parse($request->input('date_to', now()->toDateString()))->endOfDay();

        // Get sales summary
        $stats = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->groupBy('order_items.product_id')
            ->get();

        $productIds = $stats->pluck('product_id')->toArray();
        $products = Product::whereIn('id', $productIds)->pluck('name', 'id');

        $data = $stats->map(function ($row) use ($products) {
            return [
                'name' => $products[$row->product_id] ?? 'Unknown',
                'quantity' => (int) $row->total_quantity,
            ];
        });

        return response()->json([
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
            'data' => $data,
        ]);
    }




    public function productRecommendations($productId, $limit = 5)
    {
        $orderIds = DB::table('order_items')
            ->where('product_id', $productId)
            ->pluck('order_id')
            ->toArray();

        if (empty($orderIds)) {
            return response()->json(['data' => []]);
        }

        $coProducts = DB::table('order_items')
            ->select('product_id', DB::raw('COUNT(*) as co_count'))
            ->whereIn('order_id', $orderIds)
            ->where('product_id', '<>', $productId)
            ->groupBy('product_id')
            ->orderByDesc('co_count')
            ->limit($limit)
            ->get();

        $recommended = $coProducts->map(function ($item) {
            $product = Product::with('category')->find($item->product_id);
            return $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'food_type' => $product->food_type,
                'category' => $product->category ? $product->category->name : null,
                'co_order_count' => $item->co_count,
            ] : null;
        })->filter();

        return response()->json(['data' => $recommended]);
    }




    public function outOfStockProducts()
    {
        $products = Product::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->get();

        return response()->json([
            'out_of_stock_products' => $products
        ]);
    }

    public function productsByCategory($categoryId = null)
    {
        $query = Product::with('offers', 'category')->orderBy('id', 'desc');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->get();

        return response()->json([
            'category_id' => $categoryId,
            'products' => $products
        ]);
    }

    public function productsByFoodType($type)
    {
        $query = Product::with('offers', 'category')->orderBy('id', 'desc');
        if ($type) {
            $query->where('food_type', $type);
        }

        $products = $query->get();

        return response()->json([
            'type' => $type,
            'products' => $products
        ]);
    }

    public function productsByCourseType($type)
    {
        $query = Product::with('offers', 'category')->orderBy('id', 'desc');
        if ($type) {
            $query->where('course_type', $type);
        }

        $products = $query->get();

        return response()->json([
            'type' => $type,
            'products' => $products
        ]);
    }

    public function searchProducts(Request $request)
    {
        $queryText = $request->query('q');

        $query = Product::with('offers', 'category')->orderBy('id', 'desc');

        if ($queryText) {
            $query->where(function ($q) use ($queryText) {
                $q->where('name', 'LIKE', "%{$queryText}%")
                    ->orWhere('description', 'LIKE', "%{$queryText}%")
                    ->orWhereHas('category', function ($catQuery) use ($queryText) {
                        $catQuery->where('name', 'LIKE', "%{$queryText}%");
                    });
            });
        }

        $products = $query->get();

        return response()->json([
            'query' => $queryText,
            'results' => $products
        ]);
    }
}
