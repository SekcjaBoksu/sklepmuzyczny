<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
        ]);

        $product->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return back()->with('success', 'Review added successfully!');
    }

    public function destroy(Review $review)
    {
        if (auth()->id() !== $review->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        $review->delete();

        return back()->with('success', 'Review deleted successfully!');
    }
}
