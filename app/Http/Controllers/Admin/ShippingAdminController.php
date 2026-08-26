<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShippingCarrier;
use App\Models\ShippingRate;
use App\Models\ShippingService;
use App\Models\ShippingZone;
use App\Models\ShippingZoneLocation;
use App\Services\ShippingService as ShippingManager;
use Illuminate\Http\Request;

class ShippingAdminController extends Controller
{
    public function __construct(
        private readonly ShippingManager $shipping
    ) {
    }

    public function index()
    {
        $carriers = ShippingCarrier::query()
            ->with('services')
            ->latest()
            ->get();

        $zones = ShippingZone::query()
            ->with(['locations', 'rates.service'])
            ->latest()
            ->get();

        $shipments = Shipment::query()
            ->with(['order', 'carrier', 'service'])
            ->latest()
            ->paginate(30);

        return view('admin.shipping.index', compact(
            'carriers',
            'zones',
            'shipments'
        ));
    }

    public function storeCarrier(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:160'],
            'name_en' => ['nullable', 'string', 'max:160'],
            'code' => ['required', 'string', 'max:80', 'unique:shipping_carriers,code'],
            'tracking_url_template' => ['nullable', 'string', 'max:500'],
            'integration_type' => ['required', 'in:manual,api,webhook,aggregator'],
            'supports_cod' => ['nullable', 'boolean'],
            'supports_return_pickup' => ['nullable', 'boolean'],
        ]);

        ShippingCarrier::create([
            ...$validated,
            'supports_cod' => (bool) ($validated['supports_cod'] ?? false),
            'supports_return_pickup' => (bool) (
                $validated['supports_return_pickup'] ?? false
            ),
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء شركة الشحن.');
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'shipping_carrier_id' => ['nullable', 'exists:shipping_carriers,id'],
            'name_ar' => ['required', 'string', 'max:160'],
            'name_en' => ['nullable', 'string', 'max:160'],
            'code' => ['required', 'string', 'max:100', 'unique:shipping_services,code'],
            'estimated_days_min' => ['nullable', 'integer', 'min:0'],
            'estimated_days_max' => ['nullable', 'integer', 'min:0'],
            'supports_cod' => ['nullable', 'boolean'],
        ]);

        ShippingService::create([
            ...$validated,
            'supports_cod' => (bool) ($validated['supports_cod'] ?? false),
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء خدمة الشحن.');
    }

    public function storeZone(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:160'],
            'name_en' => ['nullable', 'string', 'max:160'],
            'code' => ['required', 'string', 'max:100', 'unique:shipping_zones,code'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'governorate' => ['nullable', 'string', 'max:160'],
            'wilayat' => ['nullable', 'string', 'max:160'],
        ]);

        $zone = ShippingZone::create([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'] ?? null,
            'code' => $validated['code'],
            'is_active' => true,
        ]);

        if (
            !empty($validated['governorate'])
            || !empty($validated['wilayat'])
        ) {
            ShippingZoneLocation::create([
                'shipping_zone_id' => $zone->id,
                'country_code' => strtoupper($validated['country_code'] ?? 'OM'),
                'governorate' => $validated['governorate'] ?? null,
                'wilayat' => $validated['wilayat'] ?? null,
            ]);
        }

        return back()->with('success', 'تم إنشاء منطقة الشحن.');
    }

    public function storeRate(Request $request)
    {
        $validated = $request->validate([
            'shipping_zone_id' => ['required', 'exists:shipping_zones,id'],
            'shipping_service_id' => ['required', 'exists:shipping_services,id'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_weight_kg' => ['nullable', 'numeric', 'min:0'],
            'extra_kg_price' => ['nullable', 'numeric', 'min:0'],
            'cod_available' => ['nullable', 'boolean'],
            'cod_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        ShippingRate::updateOrCreate(
            [
                'shipping_zone_id' => $validated['shipping_zone_id'],
                'shipping_service_id' => $validated['shipping_service_id'],
            ],
            [
                'base_price' => $validated['base_price'],
                'free_shipping_threshold' => $validated['free_shipping_threshold'] ?? null,
                'min_order_amount' => $validated['min_order_amount'] ?? 0,
                'max_order_amount' => $validated['max_order_amount'] ?? null,
                'max_weight_kg' => $validated['max_weight_kg'] ?? null,
                'extra_kg_price' => $validated['extra_kg_price'] ?? 0,
                'cod_available' => (bool) ($validated['cod_available'] ?? false),
                'cod_fee' => $validated['cod_fee'] ?? 0,
                'is_active' => true,
            ]
        );

        return back()->with('success', 'تم حفظ سعر الشحن.');
    }

    public function createShipment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'shipping_carrier_id' => ['nullable', 'exists:shipping_carriers,id'],
            'shipping_service_id' => ['nullable', 'exists:shipping_services,id'],
            'shipping_zone_id' => ['nullable', 'exists:shipping_zones,id'],
            'tracking_number' => ['nullable', 'string', 'max:180'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'cod_amount' => ['nullable', 'numeric', 'min:0'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'estimated_delivery_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.order_item_id' => ['required', 'exists:order_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $this->shipping->createShipment(
            $order,
            $validated,
            $validated['items']
        );

        return back()->with('success', 'تم إنشاء الشحنة.');
    }

    public function updateShipmentStatus(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,ready,picked_up,in_transit,out_for_delivery,delivered,failed_delivery,returned_to_sender,cancelled',
            ],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $this->shipping->updateStatus(
            $shipment,
            $validated['status'],
            $validated['location'] ?? null,
            $validated['description'] ?? null
        );

        return back()->with('success', 'تم تحديث حالة الشحنة.');
    }
}
