<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Offer::with('product')->get();
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
            'title' => 'required|string|max:255',
            'type' => 'required|in:global,product',
            'product_id' => 'nullable|required_if:type,product|exists:products,id',
            'offer_kind' => 'required|in:percentage,fixed_amount,buy_x_get_y',
            'value' => 'nullable|numeric|min:0',
            'buy_quantity' => 'nullable|integer|min:1|required_if:offer_kind,buy_x_get_y',
            'get_quantity' => 'nullable|integer|min:1|required_if:offer_kind,buy_x_get_y',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'max_usage' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validated['offer_kind'] !== 'buy_x_get_y' && empty($validated['value'])) {
            return response()->json([
                'error' => 'The value field is required for percentage or fixed_amount offers.'
            ], 422);
        }

        if ($validated['type'] === 'global' && isset($validated['product_id'])) {
            $validated['product_id'] = null;
        }

        $offer = Offer::create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'product_id' => $validated['product_id'] ?? null,
            'offer_kind' => $validated['offer_kind'],
            'value' => $validated['value'] ?? null,
            'buy_quantity' => $validated['buy_quantity'] ?? null,
            'get_quantity' => $validated['get_quantity'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'max_usage' => $validated['max_usage'] ?? 5,
            'num_used' => 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Offer created successfully.',
            'offer' => $offer
        ], 201);
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
    public function update(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:global,product',
            'product_id' => 'nullable|required_if:type,product|exists:products,id',
            'offer_kind' => 'sometimes|required|in:percentage,fixed_amount,buy_x_get_y',
            'value' => 'nullable|numeric|min:0',
            'buy_quantity' => 'nullable|integer|min:1|required_if:offer_kind,buy_x_get_y',
            'get_quantity' => 'nullable|integer|min:1|required_if:offer_kind,buy_x_get_y',
            'start_date' => 'sometimes|required|date|before:end_date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'max_usage' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($validated['offer_kind']) && $validated['offer_kind'] !== 'buy_x_get_y' && empty($validated['value'])) {
            return response()->json([
                'error' => 'The value field is required for percentage or fixed_amount offers.'
            ], 422);
        }

        if (isset($validated['type']) && $validated['type'] === 'global') {
            $validated['product_id'] = null;
        }

        $offer->update([
            'title' => $validated['title'] ?? $offer->title,
            'type' => $validated['type'] ?? $offer->type,
            'product_id' => $validated['product_id'] ?? $offer->product_id,
            'offer_kind' => $validated['offer_kind'] ?? $offer->offer_kind,
            'value' => $validated['value'] ?? $offer->value,
            'buy_quantity' => $validated['buy_quantity'] ?? $offer->buy_quantity,
            'get_quantity' => $validated['get_quantity'] ?? $offer->get_quantity,
            'start_date' => $validated['start_date'] ?? $offer->start_date,
            'end_date' => $validated['end_date'] ?? $offer->end_date,
            'max_usage' => $validated['max_usage'] ?? $offer->max_usage,
            'is_active' => $validated['is_active'] ?? $offer->is_active,
        ]);

        return response()->json([
            'message' => 'Offer updated successfully.',
            'offer' => $offer->fresh()
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
