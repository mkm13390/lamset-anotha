<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShippingService
{
    public function calculateRate(
        ShippingZone $zone,
        int $shippingServiceId,
        float $orderAmount,
        ?float $weightKg = null,
        bool $cod = false
    ): array {
        $rate = ShippingRate::query()
            ->where('shipping_zone_id', $zone->id)
            ->where('shipping_service_id', $shippingServiceId)
            ->where('is_active', true)
            ->first();

        if (!$rate) {
            throw ValidationException::withMessages([
                'shipping' => 'لا يوجد سعر شحن متاح لهذه المنطقة والخدمة.',
            ]);
        }

        if ((float) $rate->min_order_amount > $orderAmount) {
            throw ValidationException::withMessages([
                'shipping' => 'قيمة الطلب أقل من الحد المطلوب لخدمة الشحن.',
            ]);
        }

        if (
            $rate->max_order_amount !== null
            && $orderAmount > (float) $rate->max_order_amount
        ) {
            throw ValidationException::withMessages([
                'shipping' => 'قيمة الطلب أعلى من الحد المسموح لخدمة الشحن.',
            ]);
        }

        $shippingCost = (float) $rate->base_price;

        if (
            $rate->free_shipping_threshold !== null
            && $orderAmount >= (float) $rate->free_shipping_threshold
        ) {
            $shippingCost = 0;
        }

        if (
            $weightKg !== null
            && $rate->max_weight_kg !== null
            && $weightKg > (float) $rate->max_weight_kg
        ) {
            $extraKg = $weightKg - (float) $rate->max_weight_kg;
            $shippingCost += $extraKg * (float) $rate->extra_kg_price;
        }

        $codFee = 0;

        if ($cod) {
            if (!$rate->cod_available) {
                throw ValidationException::withMessages([
                    'shipping' => 'الدفع عند الاستلام غير متاح لهذه الخدمة.',
                ]);
            }

            $codFee = (float) $rate->cod_fee;
        }

        return [
            'rate' => $rate,
            'shipping_cost' => round($shippingCost, 3),
            'cod_fee' => round($codFee, 3),
            'total_shipping' => round($shippingCost + $codFee, 3),
        ];
    }

    public function createShipment(
        Order $order,
        array $data,
        array $items
    ): Shipment {
        return DB::transaction(function () use ($order, $data, $items) {
            do {
                $shipmentNumber = 'SHP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            } while (Shipment::where('shipment_number', $shipmentNumber)->exists());

            $shipment = Shipment::create([
                'shipment_number' => $shipmentNumber,
                'order_id' => $order->id,
                'shipping_carrier_id' => $data['shipping_carrier_id'] ?? null,
                'shipping_service_id' => $data['shipping_service_id'] ?? null,
                'shipping_zone_id' => $data['shipping_zone_id'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'shipping_cost' => $data['shipping_cost'] ?? 0,
                'cod_amount' => $data['cod_amount'] ?? 0,
                'weight_kg' => $data['weight_kg'] ?? null,
                'estimated_delivery_at' => $data['estimated_delivery_at'] ?? null,
                'shipping_address_snapshot' => $data['shipping_address_snapshot'] ?? null,
                'external_reference' => $data['external_reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $shipment->items()->create([
                    'order_item_id' => $item['order_item_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            ShipmentEvent::create([
                'shipment_id' => $shipment->id,
                'status' => $shipment->status,
                'description' => 'تم إنشاء الشحنة',
                'occurred_at' => now(),
            ]);

            return $shipment->load([
                'carrier',
                'service',
                'zone',
                'items.orderItem',
                'events',
            ]);
        });
    }

    public function updateStatus(
        Shipment $shipment,
        string $status,
        ?string $location = null,
        ?string $description = null
    ): Shipment {
        return DB::transaction(function () use (
            $shipment,
            $status,
            $location,
            $description
        ) {
            $updates = [
                'status' => $status,
            ];

            if (
                in_array($status, ['picked_up', 'in_transit'], true)
                && !$shipment->shipped_at
            ) {
                $updates['shipped_at'] = now();
            }

            if ($status === 'delivered') {
                $updates['delivered_at'] = now();
            }

            $shipment->update($updates);

            ShipmentEvent::create([
                'shipment_id' => $shipment->id,
                'status' => $status,
                'location' => $location,
                'description' => $description,
                'occurred_at' => now(),
            ]);

            return $shipment->refresh()->load('events');
        });
    }
}
