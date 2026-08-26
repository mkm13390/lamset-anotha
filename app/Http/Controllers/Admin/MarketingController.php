<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Models\CustomerSegment;
use App\Models\GiftCard;
use App\Models\LoyaltyTier;
use App\Models\MarketingAttribution;
use App\Models\MarketingCampaign;
use App\Models\ProductBundle;
use App\Models\ProductBundleItem;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketingController extends Controller
{
    public function index()
    {
        $stats = [
            'campaigns' => MarketingCampaign::count(),
            'active_promotions' => Promotion::where('is_active', true)->count(),
            'gift_cards_balance' => round(
                (float) GiftCard::where('status', 'active')->sum('current_balance'),
                3
            ),
            'abandoned_carts' => AbandonedCart::where('status', 'abandoned')->count(),
            'attributed_revenue' => round((float) MarketingAttribution::sum('revenue'), 3),
            'estimated_profit' => round((float) MarketingAttribution::sum('estimated_profit'), 3),
        ];

        $campaigns = MarketingCampaign::query()
            ->with(['segment', 'promotion'])
            ->latest()
            ->limit(20)
            ->get();

        $promotions = Promotion::query()
            ->latest()
            ->limit(20)
            ->get();

        $segments = CustomerSegment::query()
            ->where('is_active', true)
            ->orderBy('name_ar')
            ->get();

        $tiers = LoyaltyTier::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('admin.marketing.index', compact(
            'stats',
            'campaigns',
            'promotions',
            'segments',
            'tiers'
        ));
    }

    public function storePromotion(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:180'],
            'name_en' => ['nullable', 'string', 'max:180'],
            'code' => ['nullable', 'string', 'max:100', 'unique:promotions,code'],
            'promotion_type' => [
                'required',
                'in:automatic,coupon,flash_deal,bundle,loyalty,win_back,vip,referral',
            ],
            'discount_type' => [
                'required',
                'in:percentage,fixed_amount,free_shipping,bonus_points,store_credit,gift_item',
            ],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'minimum_spend' => ['nullable', 'numeric', 'min:0'],
            'minimum_quantity' => ['nullable', 'integer', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_customer_limit' => ['nullable', 'integer', 'min:1'],
            'is_stackable' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        Promotion::create([
            ...$validated,
            'minimum_spend' => $validated['minimum_spend'] ?? 0,
            'minimum_quantity' => $validated['minimum_quantity'] ?? 0,
            'is_stackable' => (bool) ($validated['is_stackable'] ?? false),
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء العرض التسويقي.');
    }

    public function storeSegment(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:160'],
            'name_en' => ['nullable', 'string', 'max:160'],
            'code' => ['required', 'string', 'max:100', 'unique:customer_segments,code'],
            'segment_type' => ['required', 'in:manual,rule_based,ai'],
        ]);

        CustomerSegment::create([
            ...$validated,
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء شريحة العملاء.');
    }

    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'channel' => [
                'required',
                'in:whatsapp,email,sms,push,instagram,web,mixed',
            ],
            'customer_segment_id' => [
                'nullable',
                'integer',
                'exists:customer_segments,id',
            ],
            'promotion_id' => [
                'nullable',
                'integer',
                'exists:promotions,id',
            ],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'scheduled_at' => ['nullable', 'date'],
            'message_ar' => ['nullable', 'string', 'max:5000'],
            'message_en' => ['nullable', 'string', 'max:5000'],
        ]);

        MarketingCampaign::create([
            ...$validated,
            'budget' => $validated['budget'] ?? 0,
            'status' => $validated['scheduled_at'] ? 'scheduled' : 'draft',
            'utm_source' => $validated['channel'],
            'utm_medium' => 'campaign',
            'utm_campaign' => Str::slug($validated['name']),
        ]);

        return back()->with('success', 'تم إنشاء الحملة.');
    }

    public function bundles()
    {
        $bundles = ProductBundle::query()
            ->with('items.variant.product')
            ->latest()
            ->paginate(30);

        $variants = ProductVariant::query()
            ->with('product')
            ->where('is_active', true)
            ->whereHas('product', fn ($q) => $q->where('is_active', true))
            ->orderBy('id')
            ->get();

        return view('admin.marketing.bundles', compact(
            'bundles',
            'variants'
        ));
    }

    public function storeBundle(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:180'],
            'name_en' => ['nullable', 'string', 'max:180'],
            'pricing_type' => [
                'required',
                'in:fixed_price,percentage_discount,amount_discount',
            ],
            'value' => ['required', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'description_ar' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $baseSlug = Str::slug($validated['name_en'] ?: $validated['name_ar']);
        $slug = $baseSlug ?: 'bundle-' . time();
        $counter = 1;

        while (ProductBundle::where('slug', $slug)->exists()) {
            $slug = ($baseSlug ?: 'bundle') . '-' . $counter++;
        }

        $bundle = ProductBundle::create([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'] ?? null,
            'slug' => $slug,
            'pricing_type' => $validated['pricing_type'],
            'value' => $validated['value'],
            'is_active' => true,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'description_ar' => $validated['description_ar'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            ProductBundleItem::create([
                'product_bundle_id' => $bundle->id,
                'product_variant_id' => $item['product_variant_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return back()->with('success', 'تم إنشاء الباقة.');
    }
}
