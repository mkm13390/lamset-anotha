<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:150'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = ProductReview::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->whereNull('order_id')
            ->first();

        if ($review) {
            $review->update([
                ...$data,
                'is_approved' => false,
                'approved_at' => null,
                'is_visible' => true,
            ]);
        } else {
            ProductReview::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'order_id' => null,
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'review' => $data['review'] ?? null,
                'is_verified_purchase' => false,
                'is_approved' => false,
                'is_visible' => true,
            ]);
        }

        return back()->with(
            'success',
            'تم حفظ تقييمك وسيظهر بعد مراجعته من الإدارة.'
        );
    }

    public function update(
        Request $request,
        ProductReview $review
    ): RedirectResponse {
        abort_unless($review->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:150'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        $review->update([
            ...$data,
            'is_approved' => false,
            'approved_at' => null,
        ]);

        return back()->with(
            'success',
            'تم تحديث تقييمك وسيعاد مراجعته من الإدارة.'
        );
    }

    public function destroy(
        Request $request,
        ProductReview $review
    ): RedirectResponse {
        abort_unless($review->user_id === $request->user()->id, 403);

        $review->delete();

        return back()->with('success', 'تم حذف التقييم.');
    }
}
