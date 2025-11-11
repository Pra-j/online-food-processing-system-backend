<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function productSummary(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = OrderItem::query();

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Total quantity sold and total revenue
        $totals = $query->select(
            DB::raw('SUM(quantity) as total_quantity_sold'),
            DB::raw('SUM(total_product_price) as total_revenue')
        )->first();

        // Highest sold product
        $highest = $query->select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity')
        )
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->first();

        $highestProduct = $highest ? Product::find($highest->product_id) : null;

        return response()->json([
            'total_quantity_sold' => $totals->total_quantity_sold ?? 0,
            'total_revenue' => $totals->total_revenue ?? 0,
            'heighest_sold_product' => $highestProduct ? $highestProduct->name : null,
            'heighest_sold_product_quantity' => $highest->total_quantity ?? 0,
        ]);
    }
}
