<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::with('offers', 'category', 'media')
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
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|integer|exists:categories,id',
            'stock' => 'required|integer',
            'food_type' => 'required|in:veg,non-veg,drinks',
            'course_type' => 'required|in:appetizer,main,dessert',
            'image' => 'required|file|mimes:jpg,jpeg,png,webp,gif,svg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $media_id = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                if (!$file->isValid()) {
                    throw new \Exception('Invalid file upload');
                }

                $folder = 'products/' . date('Y') . '/' . date('m');
                $filename = \Illuminate\Support\Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($folder, $filename, 'public');

                $media = Media::create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'disk' => 'public',
                ]);

                $media_id = $media->id;
            }

            unset($validated['image']);

            $product = Product::create([
                ...$validated,
                'media_id' => $media_id
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Product created successfully',
                'product' => $product->load('media', 'category')
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Product::with('category', 'offers', 'media')->where('id', $id)->first();
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
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'category_id' => 'nullable|integer|exists:categories,id',
            'stock' => 'nullable|integer',
            'food_type' => 'nullable|in:veg,non-veg,drinks',
            'course_type' => 'nullable|in:appetizer,main,dessert',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:10240'
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $folder = 'products/' . date('Y') . '/' . date('m');
                $filename = \Illuminate\Support\Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($folder, $filename, 'public');

                $media = Media::create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'disk' => 'public',
                ]);

                // delete previous media if exists
                if ($product->media) {
                    Storage::disk($product->media->disk)->delete($product->media->file_path);
                    $product->media->delete();
                }

                $validated['media_id'] = $media->id;
            }

            unset($validated['image']);
            $product->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Product updated',
                'product' => $product->fresh()->load('media', 'category', 'offers')
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Delete associated media if exists
        if ($product->media) {
            Storage::disk($product->media->disk)->delete($product->media->file_path);
            $product->media->delete();
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }

    public function productsStatus(string $id, $startDate = null, $endDate = null)
    {
        $product = Product::with('media')->find($id);

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
            'media' => $product->media,
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
        $products = Product::with('media')->whereIn('id', $productIds)->get()->keyBy('id');

        $data = $stats->map(function ($row) use ($products) {
            $product = $products[$row->product_id] ?? null;
            return [
                'name' => $product ? $product->name : 'Unknown',
                'quantity' => (int) $row->total_quantity,
                'media' => $product ? $product->media : null,
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
            $product = Product::with('category', 'media')->find($item->product_id);
            return $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'food_type' => $product->food_type,
                'category' => $product->category ? $product->category->name : null,
                'co_order_count' => $item->co_count,
                'media' => $product->media,
            ] : null;
        })->filter();

        return response()->json(['data' => $recommended]);
    }


    public function outOfStockProducts()
    {
        $products = Product::with('media', 'category')
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->get();

        return response()->json([
            'out_of_stock_products' => $products
        ]);
    }

    public function productsByCategory($categoryId = null)
    {
        $query = Product::with('offers', 'category', 'media')->orderBy('id', 'desc');

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
        $query = Product::with('offers', 'category', 'media')->orderBy('id', 'desc');
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
        $query = Product::with('offers', 'category', 'media')->orderBy('id', 'desc');
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

        $query = Product::with('offers', 'category', 'media')->orderBy('id', 'desc');

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

    public function search(Request $request)
    {
        $q = trim($request->query('q', ''));

        if ($q === '') {
            return response()->json(['data' => []], 200);
        }

        $qLike = '%' . $q . '%';

        $products = Product::with('media', 'category')
            ->where(function ($w) use ($qLike) {
                $w->where('name', 'like', $qLike)
                    ->orWhere('description', 'like', $qLike)
                    ->orWhere('food_type', 'like', $qLike)
                    ->orWhereHas('category', function ($c) use ($qLike) {
                        $c->where('name', 'like', $qLike);
                    });
            })
            ->where('is_active', true)
            ->orderByRaw("
            CASE
                WHEN name LIKE ? THEN 3
                WHEN description LIKE ? THEN 2
                ELSE 1
            END DESC
        ", [$qLike, $qLike])
            ->limit(20)
            ->get();

        return response()->json([
            'data' => $products
        ], 200);
    }
}
