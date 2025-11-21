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

    /**
     * We start the algorithm here.
     * Product-to-Product Recommendation using Cosine Similarity
     * Steps:
     * 1. Build Order-Product Matrix (orders x products)
     * 2. Calculate cosine similarity between products
     * 3. Find products with highest similarity to the source product
     * 4. Return top N recommendations
     */
    public function productRecommendations($productId, $limit = 5)
    {
        Log::info("=================================================================");
        Log::info("PRODUCT RECOMMENDATION CALCULATION - Product ID: {$productId}");
        Log::info("=================================================================");

        $sourceProduct = Product::find($productId);

        if (!$sourceProduct) {
            Log::warning("Product ID {$productId} not found");
            return response()->json(['data' => []]);
        }

        // Check if product has order history
        $hasOrders = DB::table('order_items')
            ->where('product_id', $productId)
            ->exists();

        if (!$hasOrders) {
            Log::info("Product ID {$productId} has no order history, using fallback recommendations");
            return $this->fallbackRecommendations($sourceProduct, $limit);
        }

        // Step 1: Build the order-product matrix
        Log::info("\n--- STEP 1: Building Order-Product Matrix ---");

        $orderProductData = DB::table('order_items')
            ->select('order_id', 'product_id', DB::raw('SUM(quantity) as quantity'))
            ->groupBy('order_id', 'product_id')
            ->get();

        // Build matrix: orders as rows, products as columns
        $matrix = [];
        $productIds = [];

        foreach ($orderProductData as $row) {
            $matrix[$row->order_id][$row->product_id] = $row->quantity;
            $productIds[$row->product_id] = true;
        }

        $productIds = array_keys($productIds);

        // Log the matrix in tabular format
        $this->logOrderProductMatrix($matrix, $productIds);

        // Check if source product is in the matrix
        if (!in_array($productId, $productIds)) {
            Log::warning("Source product {$productId} not found in order matrix");
            return $this->fallbackRecommendations($sourceProduct, $limit);
        }

        // Step 2: Calculate cosine similarity between source product and all other products
        Log::info("\n--- STEP 2: Calculating Cosine Similarities ---");

        $similarities = [];
        $similarityDetails = [];

        foreach ($productIds as $targetProductId) {
            if ($targetProductId == $productId) {
                continue; // Skip self
            }

            // Calculate cosine similarity with detailed logging
            $details = $this->calculateCosineSimilarityWithDetails($matrix, $productId, $targetProductId);
            $similarity = $details['similarity'];

            if ($similarity > 0) {
                $similarities[$targetProductId] = $similarity;
                $similarityDetails[$targetProductId] = $details;
            }
        }

        // Log cosine similarity results in tabular format
        $this->logCosineSimilarities($productId, $similarities, $similarityDetails);

        // Sort by similarity (highest first)
        arsort($similarities);

        // Step 3: Get top similar products
        Log::info("\n--- STEP 3: Selecting Top Recommendations ---");

        $topSimilarIds = array_slice(array_keys($similarities), 0, $limit * 2, true);

        if (empty($topSimilarIds)) {
            Log::info("No similar products found, using fallback recommendations");
            return $this->fallbackRecommendations($sourceProduct, $limit);
        }

        // Fetch product details and filter
        $recommendations = Product::with('category', 'media')
            ->whereIn('id', $topSimilarIds)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->get()
            ->map(function ($product) use ($similarities, $productId) {
                $similarity = $similarities[$product->id];

                // Get co-occurrence count
                $coCount = DB::table('order_items as oi1')
                    ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
                    ->where('oi1.product_id', $productId)
                    ->where('oi2.product_id', $product->id)
                    ->distinct('oi1.order_id')
                    ->count('oi1.order_id');

                // Calculate final score combining cosine similarity and business rules
                $score = $this->calculateFinalScore($product, $similarity, $coCount);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'food_type' => $product->food_type,
                    'course_type' => $product->course_type,
                    'category' => $product->category ? $product->category->name : null,
                    'co_order_count' => $coCount,
                    'avg_quantity' => 0,
                    'recommendation_score' => $score,
                    'media' => $product->media,
                ];
            })
            ->sortByDesc('recommendation_score')
            ->take($limit)
            ->values();

        // Log final recommendations
        $this->logFinalRecommendations($recommendations);

        // Supplement with fallback if needed
        if ($recommendations->count() < $limit) {
            Log::info("Adding fallback recommendations to reach limit of {$limit}");
            $fallback = $this->fallbackRecommendations(
                $sourceProduct,
                $limit - $recommendations->count(),
                $recommendations->pluck('id')->toArray()
            );

            $recommendations = $recommendations->merge($fallback->getData()->data);
        }

        Log::info("=================================================================");
        Log::info("RECOMMENDATION CALCULATION COMPLETED");
        Log::info("=================================================================\n");

        return response()->json(['data' => $recommendations]);
    }

    /**
     * Log Order-Product Matrix in tabular format
     */
    private function logOrderProductMatrix($matrix, $productIds)
    {
        Log::info("Order-Product Matrix (Orders x Products):");
        Log::info("Total Orders: " . count($matrix) . " | Total Products: " . count($productIds));

        // Create header
        $header = str_pad("Order ID", 12) . "| " . implode(" | ", array_map(fn($pid) => str_pad("P{$pid}", 8), array_slice($productIds, 0, 10)));
        Log::info($header);
        Log::info(str_repeat("-", strlen($header)));

        // Log first 20 orders for readability
        $orderCount = 0;
        foreach ($matrix as $orderId => $products) {
            if ($orderCount++ >= 20) {
                Log::info("... (showing first 20 orders only)");
                break;
            }

            $row = str_pad("Order {$orderId}", 12) . "| ";
            $values = [];
            foreach (array_slice($productIds, 0, 10) as $pid) {
                $values[] = str_pad($products[$pid] ?? '0', 8);
            }
            $row .= implode(" | ", $values);
            Log::info($row);
        }
        Log::info("");
    }

    /**
     * Calculate Cosine Similarity with detailed information
     */
    private function calculateCosineSimilarityWithDetails($matrix, $productA, $productB)
    {
        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;

        // Iterate through all orders
        foreach ($matrix as $orderId => $products) {
            $valueA = $products[$productA] ?? 0;
            $valueB = $products[$productB] ?? 0;

            $dotProduct += $valueA * $valueB;
            $magnitudeA += $valueA * $valueA;
            $magnitudeB += $valueB * $valueB;
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        // Avoid division by zero
        if ($magnitudeA == 0 || $magnitudeB == 0) {
            $similarity = 0;
        } else {
            $similarity = $dotProduct / ($magnitudeA * $magnitudeB);
        }

        return [
            'similarity' => $similarity,
            'dot_product' => $dotProduct,
            'magnitude_a' => round($magnitudeA, 4),
            'magnitude_b' => round($magnitudeB, 4),
        ];
    }

    /**
     * Log Cosine Similarities in tabular format
     */
    private function logCosineSimilarities($sourceProductId, $similarities, $details)
    {
        Log::info("Cosine Similarity Calculations for Product ID: {$sourceProductId}");

        $header = str_pad("Target Product", 16) . "| " .
            str_pad("Dot Product", 14) . "| " .
            str_pad("Magnitude A", 14) . "| " .
            str_pad("Magnitude B", 14) . "| " .
            str_pad("Similarity", 12);

        Log::info($header);
        Log::info(str_repeat("-", strlen($header)));

        // Sort by similarity for better readability
        arsort($similarities);

        foreach ($similarities as $targetId => $similarity) {
            $detail = $details[$targetId];

            $row = str_pad("Product {$targetId}", 16) . "| " .
                str_pad(number_format($detail['dot_product'], 2), 14) . "| " .
                str_pad($detail['magnitude_a'], 14) . "| " .
                str_pad($detail['magnitude_b'], 14) . "| " .
                str_pad(number_format($similarity, 6), 12);

            Log::info($row);
        }

        Log::info("\nTop 5 Most Similar Products:");
        $topFive = array_slice($similarities, 0, 5, true);
        foreach ($topFive as $productId => $sim) {
            Log::info("  Product {$productId}: " . number_format($sim, 6));
        }
        Log::info("");
    }

    /**
     * Log Final Recommendations
     */
    private function logFinalRecommendations($recommendations)
    {
        Log::info("Final Recommendations with Scores:");

        $header = str_pad("Product ID", 12) . "| " .
            str_pad("Product Name", 25) . "| " .
            str_pad("Co-Orders", 12) . "| " .
            str_pad("Final Score", 13);

        Log::info($header);
        Log::info(str_repeat("-", strlen($header)));

        foreach ($recommendations as $rec) {
            $row = str_pad("P{$rec['id']}", 12) . "| " .
                str_pad(substr($rec['name'], 0, 24), 25) . "| " .
                str_pad($rec['co_order_count'], 12) . "| " .
                str_pad(number_format($rec['recommendation_score'], 2), 13);

            Log::info($row);
        }
        Log::info("");
    }

    /**
     * Calculate Cosine Similarity between two products based on order matrix
     * Cosine Similarity = (A · B) / (||A|| * ||B||)
     */
    private function calculateCosineSimilarity($matrix, $productA, $productB)
    {
        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;

        // Iterate through all orders
        foreach ($matrix as $orderId => $products) {
            $valueA = $products[$productA] ?? 0;
            $valueB = $products[$productB] ?? 0;

            $dotProduct += $valueA * $valueB;
            $magnitudeA += $valueA * $valueA;
            $magnitudeB += $valueB * $valueB;
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        // Avoid division by zero
        if ($magnitudeA == 0 || $magnitudeB == 0) {
            return 0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }

    /**
     * Calculate final recommendation score
     * Combines cosine similarity with business rules
     */
    private function calculateFinalScore($product, $cosineSimilarity, $coCount)
    {
        // Base score from cosine similarity (0-100 scale)
        $score = $cosineSimilarity * 70; // 70% weight to cosine similarity

        // Bonus for co-occurrence frequency (up to 20 points)
        $score += min($coCount * 2, 20);

        // Bonus for complementary course types (10 points)
        // This is handled in business logic layer

        return round($score, 2);
    }

    /**
     * Check if two course types complement each other
     */
    private function areComplementaryCourses($courseType1, $courseType2)
    {
        $complementaryPairs = [
            'appetizer' => ['main', 'dessert'],
            'main' => ['appetizer', 'dessert'],
            'dessert' => ['appetizer', 'main'],
        ];

        return in_array($courseType2, $complementaryPairs[$courseType1] ?? []);
    }

    /**
     * Fallback recommendations when no order history exists
     */
    private function fallbackRecommendations($sourceProduct, $limit, $excludeIds = [])
    {
        Log::info("=================================================================");
        Log::info("FALLBACK RECOMMENDATION CALCULATION");
        Log::info("=================================================================");
        Log::info("Source Product ID: {$sourceProduct->id}");
        Log::info("Source Product Name: {$sourceProduct->name}");
        Log::info("Source Category ID: {$sourceProduct->category_id}");
        Log::info("Source Food Type: {$sourceProduct->food_type}");
        Log::info("Source Course Type: {$sourceProduct->course_type}");
        Log::info("Source Price: " . number_format($sourceProduct->price, 2));
        Log::info("Requested Limit: {$limit}");
        Log::info("Excluded Product IDs: " . implode(', ', $excludeIds));
        Log::info("");

        $excludeIds[] = $sourceProduct->id;

        $recommendations = Product::with('category', 'media')
            // ->where('food_type', $sourceProduct->food_type)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->whereNotIn('id', $excludeIds)
            ->get()
            ->map(function ($product) use ($sourceProduct) {
                $score = 0;
                $scoreBreakdown = [];

                // Prioritize complementary courses
                if ($this->areComplementaryCourses($product->course_type, $sourceProduct->course_type)) {
                    $score += 40;
                    $scoreBreakdown[] = 'Complementary Course: +40';
                }

                // Same category products
                if ($product->category_id === $sourceProduct->category_id) {
                    $score += 30;
                    $scoreBreakdown[] = 'Same Category: +30';
                }

                // Price similarity (closer price = better match)
                $priceDiff = abs($product->price - $sourceProduct->price);
                $maxPrice = max($product->price, $sourceProduct->price);
                $priceScore = $maxPrice > 0 ? max(0, 30 - ($priceDiff / $maxPrice * 30)) : 0;
                $score += $priceScore;
                $scoreBreakdown[] = 'Price Similarity: +' . number_format($priceScore, 2);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'food_type' => $product->food_type,
                    'course_type' => $product->course_type,
                    'category' => $product->category ? $product->category->name : null,
                    'category_id' => $product->category_id,
                    'co_order_count' => 0,
                    'avg_quantity' => 0,
                    'recommendation_score' => $score,
                    'score_breakdown' => $scoreBreakdown,
                    'media' => $product->media,
                    'reason' => 'Similar product',
                ];
            })
            ->sortByDesc('recommendation_score')
            ->take($limit)
            ->values();

        // Log fallback calculation details
        $this->logFallbackCalculations($sourceProduct, $recommendations);

        Log::info("=================================================================");
        Log::info("FALLBACK RECOMMENDATION COMPLETED");
        Log::info("=================================================================\n");

        // Remove score_breakdown from final response
        $finalRecommendations = $recommendations->map(function ($rec) {
            unset($rec['score_breakdown']);
            unset($rec['category_id']);
            return $rec;
        });

        return response()->json(['data' => $finalRecommendations]);
    }

    /**
     * Log Fallback Recommendations Calculation Details
     */
    private function logFallbackCalculations($sourceProduct, $recommendations)
    {
        Log::info("--- Fallback Scoring Breakdown ---");
        Log::info("Scoring Rules:");
        Log::info("  - Complementary Course Type: +40 points");
        Log::info("  - Same Category: +30 points");
        Log::info("  - Price Similarity: up to +30 points (based on price difference)");
        Log::info("");

        $header = str_pad("Product ID", 12) . "| " .
            str_pad("Product Name", 25) . "| " .
            str_pad("Category", 15) . "| " .
            str_pad("Course", 12) . "| " .
            str_pad("Price", 10) . "| " .
            str_pad("Score", 8);

        Log::info($header);
        Log::info(str_repeat("-", strlen($header)));

        foreach ($recommendations as $rec) {
            $row = str_pad("P{$rec['id']}", 12) . "| " .
                str_pad(substr($rec['name'], 0, 24), 25) . "| " .
                str_pad(substr($rec['category'] ?? 'N/A', 0, 14), 15) . "| " .
                str_pad($rec['course_type'], 12) . "| " .
                str_pad(number_format($rec['price'], 2), 10) . "| " .
                str_pad(number_format($rec['recommendation_score'], 2), 8);

            Log::info($row);

            // Log score breakdown
            if (!empty($rec['score_breakdown'])) {
                Log::info("  └─ " . implode(', ', $rec['score_breakdown']));
            }
        }

        Log::info("");
        Log::info("Total Recommendations Generated: " . $recommendations->count());
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
