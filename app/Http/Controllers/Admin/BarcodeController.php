<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarcodeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));

        $variants = ProductVariant::query()
            ->with('product')
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('sku', 'like', "%{$search}%")
                        ->orWhere(
                            'color_name_ar',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'color_name_en',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'size',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'product',
                            function ($productQuery) use ($search) {
                                $productQuery
                                    ->where(
                                        'name_ar',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'name_en',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                });
            })
            ->orderBy('sku')
            ->paginate(40)
            ->withQueryString();

        return view(
            'admin.inventory.barcodes.index',
            compact('variants', 'search')
        );
    }
}
