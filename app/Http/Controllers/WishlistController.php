<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $wishlistItems = Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'product.images',
                'product.category',
                'product.variants',
            ])
            ->latest()
            ->paginate(24);

        return view('account.wishlist.index', compact('wishlistItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $data['product_id'],
        ]);

        return back()->with('success', 'تمت إضافة المنتج إلى المفضلة.');
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $item = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->delete();

            return back()->with('success', 'تمت إزالة المنتج من المفضلة.');
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'تمت إضافة المنتج إلى المفضلة.');
    }

    public function destroy(Request $request, Wishlist $wishlist): RedirectResponse
    {
        abort_unless($wishlist->user_id === $request->user()->id, 403);

        $wishlist->delete();

        return back()->with('success', 'تمت إزالة المنتج من المفضلة.');
    }
}
