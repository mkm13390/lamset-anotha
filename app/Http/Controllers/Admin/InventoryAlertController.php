<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariantStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryAlertController extends Controller
{
    public function index(Request $request): View
    {
        $warehouseId = $request->integer('warehouse_id');

        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $alerts = ProductVariantStock::query()
            ->with([
                'warehouse',
                'variant.product',
            ])
            ->where('reorder_level', '>', 0)
            ->whereRaw(
                'quantity <= reserved_quantity + damaged_quantity + sample_quantity + reorder_level'
            )
            ->when(
                $warehouseId,
                fn ($query) =>
                    $query->where('warehouse_id', $warehouseId)
            )
            ->orderBy('warehouse_id')
            ->orderBy('quantity')
            ->paginate(30)
            ->withQueryString();

        $outOfStockCount = ProductVariantStock::query()
            ->whereRaw(
                'quantity <= reserved_quantity + damaged_quantity + sample_quantity'
            )
            ->when(
                $warehouseId,
                fn ($query) =>
                    $query->where('warehouse_id', $warehouseId)
            )
            ->count();

        $reorderCount = ProductVariantStock::query()
            ->where('reorder_level', '>', 0)
            ->whereRaw(
                'quantity <= reserved_quantity + damaged_quantity + sample_quantity + reorder_level'
            )
            ->when(
                $warehouseId,
                fn ($query) =>
                    $query->where('warehouse_id', $warehouseId)
            )
            ->count();

        return view(
            'admin.inventory.alerts.index',
            compact(
                'warehouses',
                'alerts',
                'warehouseId',
                'outOfStockCount',
                'reorderCount'
            )
        );
    }
}
