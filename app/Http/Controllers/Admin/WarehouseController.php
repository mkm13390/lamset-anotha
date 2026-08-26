<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(): View
    {
        $warehouses = Warehouse::query()
            ->withCount('variantStocks')
            ->withSum('variantStocks as total_quantity', 'quantity')
            ->withSum(
                'variantStocks as reserved_quantity',
                'reserved_quantity'
            )
            ->withSum(
                'variantStocks as damaged_quantity',
                'damaged_quantity'
            )
            ->withSum(
                'variantStocks as sample_quantity',
                'sample_quantity'
            )
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view(
            'admin.inventory.warehouses.index',
            compact('warehouses')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('warehouses', 'code'),
            ],
            'branch_name' => [
                'nullable',
                'string',
                'max:150',
            ],
            'governorate' => [
                'nullable',
                'string',
                'max:100',
            ],
            'wilayat' => [
                'nullable',
                'string',
                'max:100',
            ],
            'address' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        Warehouse::create([
            ...$validated,
            'is_default' => false,
            'is_active' => true,
        ]);

        return back()->with(
            'success',
            'تمت إضافة المخزن بنجاح.'
        );
    }

    public function toggle(Warehouse $warehouse): RedirectResponse
    {
        if ($warehouse->is_default && $warehouse->is_active) {
            throw ValidationException::withMessages([
                'warehouse' =>
                    'لا يمكن إيقاف المخزن الافتراضي. اجعل مخزنًا آخر افتراضيًا أولًا.',
            ]);
        }

        $warehouse->update([
            'is_active' => ! $warehouse->is_active,
        ]);

        return back()->with(
            'success',
            $warehouse->is_active
                ? 'تم تفعيل المخزن.'
                : 'تم إيقاف المخزن.'
        );
    }

    public function makeDefault(
        Warehouse $warehouse
    ): RedirectResponse {
        if (! $warehouse->is_active) {
            throw ValidationException::withMessages([
                'warehouse' =>
                    'يجب تفعيل المخزن قبل جعله المخزن الافتراضي.',
            ]);
        }

        DB::transaction(function () use ($warehouse) {
            Warehouse::query()->update([
                'is_default' => false,
            ]);

            $warehouse->update([
                'is_default' => true,
            ]);
        });

        return back()->with(
            'success',
            'تم تعيين المخزن الافتراضي.'
        );
    }
}
