<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');

        $reviews = ProductReview::query()
            ->with([
                'user',
                'product',
                'order',
            ])
            ->when($status === 'pending', function ($query) {
                $query->where('is_approved', false);
            })
            ->when($status === 'approved', function ($query) {
                $query->where('is_approved', true);
            })
            ->when($status === 'hidden', function ($query) {
                $query->where('is_visible', false);
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total' => ProductReview::count(),
            'pending' => ProductReview::where('is_approved', false)->count(),
            'approved' => ProductReview::where('is_approved', true)->count(),
            'hidden' => ProductReview::where('is_visible', false)->count(),
        ];

        return view('admin.reviews.index', compact(
            'reviews',
            'stats',
            'status'
        ));
    }

    public function approve(ProductReview $review): RedirectResponse
    {
        $review->update([
            'is_approved' => true,
            'is_visible' => true,
            'approved_at' => now(),
            'admin_note' => null,
        ]);

        return back()->with('success', 'تم اعتماد التقييم ونشره.');
    }

    public function hide(ProductReview $review): RedirectResponse
    {
        $review->update([
            'is_visible' => false,
        ]);

        return back()->with('success', 'تم إخفاء التقييم.');
    }

    public function showAgain(ProductReview $review): RedirectResponse
    {
        $review->update([
            'is_visible' => true,
        ]);

        return back()->with('success', 'تم إظهار التقييم من جديد.');
    }

    public function reject(
        Request $request,
        ProductReview $review
    ): RedirectResponse {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $review->update([
            'is_approved' => false,
            'is_visible' => false,
            'approved_at' => null,
            'admin_note' => $data['admin_note'] ?? null,
        ]);

        return back()->with('success', 'تم رفض التقييم.');
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'تم حذف التقييم نهائيًا.');
    }
}
