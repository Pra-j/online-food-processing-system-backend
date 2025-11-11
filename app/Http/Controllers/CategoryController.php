<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return  Category::all();
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
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201); // 201 = created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Category::with('products')->where('id', $id)->first();
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }

    public function getCategoryNames()
    {
        $categories = Category::select('id', 'name')->get();
        return response()->json($categories);
    }

    public function categoryStatsOverAll()
    {
        $totalProducts = \App\Models\Product::count();

        $ordersData = \App\Models\OrderItem::select(
            DB::raw('COUNT(DISTINCT order_id) as total_orders'),
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(grand_total) as total_revenue')
        )->first();

        return response()->json([
            'total_categories' => \App\Models\Category::count(),
            'total_products' => $totalProducts,
            'total_orders' => $ordersData->total_orders ?? 0,
            'total_quantity_sold' => $ordersData->total_quantity ?? 0,
            'total_revenue' => $ordersData->total_revenue ?? 0,
        ]);
    }


    public function categoryStats($id, $startDate = null, $endDate = null)
    {
        $category = Category::with('products')->findOrFail($id);
        $productIds = $category->products->pluck('id');

        $query = OrderItem::whereIn('product_id', $productIds);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $ordersData = $query->select(
            DB::raw('COUNT(DISTINCT order_id) as total_orders'),
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(grand_total) as total_revenue')
        )->first();

        $stats = [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'total_products' => $category->products->count(),
            'total_orders' => $ordersData->total_orders ?? 0,
            'total_quantity_sold' => $ordersData->total_quantity ?? 0,
            'total_revenue' => $ordersData->total_revenue ?? 0,
        ];

        return response()->json($stats);
    }


    public function categoryChartData()
    {
        $categories = Category::with('products')->get();

        $labels = [];
        $quantityData = [];
        $revenueData = [];

        foreach ($categories as $category) {
            $labels[] = $category->name;

            $productIds = $category->products->pluck('id');

            $stats = OrderItem::whereIn('product_id', $productIds)
                ->select(
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(grand_total) as total_revenue')
                )
                ->first();

            $quantityData[] = $stats->total_quantity ?? 0;
            $revenueData[] = $stats->total_revenue ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Quantity Sold',
                    'data' => $quantityData,
                ],
                [
                    'label' => 'Revenue',
                    'data' => $revenueData,
                ],
            ],
        ]);
    }

    public function categoryHourlyStats(Request $request)
    {
        $date = $request->input('date', now()->toDateString()); // default: today

        $stats = OrderItem::select(
            'products.category_id',
            DB::raw('HOUR(orders.created_at) as hour'),
            DB::raw('COUNT(DISTINCT order_items.order_id) as total_orders'),
            DB::raw('SUM(order_items.quantity) as total_quantity')
        )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', $date)
            ->groupBy('products.category_id', DB::raw('HOUR(orders.created_at)'))
            ->orderBy('hour')
            ->get();

        $categories = Category::pluck('name', 'id');

        $dataMap = [];
        foreach ($stats as $row) {
            $dataMap[$row->hour][$row->category_id] = [
                'category_id' => $row->category_id,
                'category_name' => $categories[$row->category_id] ?? 'Unknown',
                'hour' => (int) $row->hour,
                'total_orders' => (int) $row->total_orders,
                'total_quantity' => (int) $row->total_quantity,
            ];
        }

        $filled = [];
        for ($hour = 0; $hour < 24; $hour++) {
            foreach ($categories as $categoryId => $categoryName) {
                $filled[] = $dataMap[$hour][$categoryId] ?? [
                    'category_id' => $categoryId,
                    'category_name' => $categoryName,
                    'hour' => $hour,
                    'total_orders' => 0,
                    'total_quantity' => 0,
                ];
            }
        }

        return response()->json([
            'date' => $date,
            'data' => $filled,
        ]);
    }
}
