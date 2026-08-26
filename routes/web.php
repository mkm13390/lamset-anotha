<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\ReturnAdminController;
use App\Http\Controllers\Admin\BackupAdminController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\ReportsAdminController;
use App\Http\Controllers\Admin\HealthAdminController;
use App\Http\Controllers\Admin\ApprovalAdminController;
use App\Http\Controllers\Admin\SecurityAdminController;
use App\Http\Controllers\Admin\PaymentAdminController;
use App\Http\Controllers\Admin\ShippingAdminController;
use App\Http\Controllers\CustomerReturnController;

use App\Http\Controllers\Admin\CashRegisterController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\EmployeeFinanceController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\BarcodeController;
use App\Http\Controllers\Admin\InventoryAlertController;
use App\Http\Controllers\Admin\GiftCardController;
use App\Http\Controllers\Admin\LoyaltyAdminController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\AdvancedStockController;
use App\Http\Controllers\Admin\StockTransferController;
use App\Http\Controllers\Admin\StockCountController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\PosOperationsController;
use App\Http\Controllers\Admin\PosShiftController;
use App\Http\Controllers\Admin\ProductImportController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SupplierPaymentController;
use App\Http\Controllers\Admin\SupplierReturnController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\StaffController;

/*
|--------------------------------------------------------------------------
| Public Store
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $featuredProducts = \App\Models\Product::with([
        'images',
        'category',
        'variants',
    ])
        ->where('is_active', true)
        ->latest()
        ->take(8)
        ->get();

    $categories = $featuredProducts
        ->pluck('category')
        ->filter()
        ->unique('id')
        ->take(6)
        ->values();

    return view('welcome', compact(
        'featuredProducts',
        'categories'
    ));
})->name('home');

Route::get('/products', function (\Illuminate\Http\Request $request) {
    $query = \App\Models\Product::with([
        'images',
        'category',
        'variants',
    ])->where('is_active', true);

    $search = trim((string) $request->query('q', ''));

    if ($search !== '') {
        $query->where(function ($builder) use ($search) {
            $builder
                ->where('name_ar', 'like', '%' . $search . '%')
                ->orWhere('name_en', 'like', '%' . $search . '%')
                ->orWhereHas('variants', function ($variantQuery) use ($search) {
                    $variantQuery
                        ->where('sku', 'like', '%' . $search . '%')
                        ->orWhere('color_name_ar', 'like', '%' . $search . '%')
                        ->orWhere('color_name_en', 'like', '%' . $search . '%')
                        ->orWhere('size', 'like', '%' . $search . '%');
                });
        });
    }

    if ($request->filled('category')) {
        $query->where(
            'category_id',
            (int) $request->query('category')
        );
    }

    match ($request->query('sort')) {
        'price_low' => $query->orderBy('price'),
        'price_high' => $query->orderByDesc('price'),
        'oldest' => $query->oldest(),
        default => $query->latest(),
    };

    $products = $query
        ->paginate(16)
        ->withQueryString();

    $categories = \App\Models\Product::with('category')
        ->where('is_active', true)
        ->get()
        ->pluck('category')
        ->filter()
        ->unique('id')
        ->sortBy('name_ar')
        ->values();

    return view(
        'products.index',
        compact('products', 'categories')
    );
})->name('products.index');

Route::get('/products/{product}', function (\App\Models\Product $product) {
    abort_unless($product->is_active, 404);

    $product->load([
        'images',
        'category',
        'variants',
    ]);

    $relatedProducts = \App\Models\Product::with([
        'images',
        'category',
        'variants',
    ])
        ->where('is_active', true)
        ->where('id', '!=', $product->id)
        ->when(
            $product->category_id,
            fn ($query) => $query->where(
                'category_id',
                $product->category_id
            )
        )
        ->latest()
        ->take(4)
        ->get();

    return view(
        'products.show',
        compact('product', 'relatedProducts')
    );
})->name('products.show');

/*
|--------------------------------------------------------------------------
| Gift Experience
|--------------------------------------------------------------------------
*/

Route::get('/gift-cards', function () {
    return view('gifts.cards');
})->name('gift-cards.index');

Route::get('/gift-finder', function (\Illuminate\Http\Request $request) {
    $minimum = 5.000;
    $budget = max($minimum, (float) $request->query('budget', 20));

    $products = \App\Models\Product::with([
        'images',
        'category',
        'variants',
    ])
        ->where('is_active', true)
        ->latest()
        ->get()
        ->filter(function ($product) use ($budget) {
            $prices = $product->variants
                ->where('is_active', true)
                ->pluck('price')
                ->filter(fn ($price) => $price !== null)
                ->map(fn ($price) => (float) $price);

            $price = $prices->isNotEmpty()
                ? $prices->min()
                : (float) ($product->price ?? 0);

            return $price > 0 && $price <= $budget;
        })
        ->take(16)
        ->values();

    return view(
        'gifts.finder',
        compact('products', 'budget', 'minimum')
    );
})->name('gift-finder.index');

/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/

Route::get('/cart', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/cart/add', [CartController::class, 'add'])
    ->name('cart.add');

Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])
    ->name('cart.coupon.apply');

Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])
    ->name('cart.coupon.remove');

Route::patch('/cart/{variant}', [CartController::class, 'update'])
    ->whereNumber('variant')
    ->name('cart.update');

Route::delete('/cart/{variant}', [CartController::class, 'remove'])
    ->whereNumber('variant')
    ->name('cart.remove');

Route::delete('/cart', [CartController::class, 'clear'])
    ->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/

Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index');

Route::post('/checkout', [CheckoutController::class, 'store'])
    ->name('checkout.store');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'show'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Customer Account
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/account', [AccountController::class, 'index'])
        ->name('account.index');

    Route::post('/account/track', [AccountController::class, 'track'])
        ->name('account.track');

    Route::patch('/account/profile', [AccountController::class, 'updateProfile'])
        ->name('account.profile.update');

    Route::patch('/account/password', [AccountController::class, 'updatePassword'])
        ->name('account.password.update');

    /*
    |--------------------------------------------------------------------------
    | Customer Addresses
    |--------------------------------------------------------------------------
    */

    Route::get('/account/addresses', [CustomerAddressController::class, 'index'])
        ->name('account.addresses.index');

    Route::post('/account/addresses', [CustomerAddressController::class, 'store'])
        ->name('account.addresses.store');

    Route::put('/account/addresses/{address}', [CustomerAddressController::class, 'update'])
        ->name('account.addresses.update');

    Route::patch('/account/addresses/{address}/default', [CustomerAddressController::class, 'setDefault'])
        ->name('account.addresses.default');

    Route::delete('/account/addresses/{address}', [CustomerAddressController::class, 'destroy'])
        ->name('account.addresses.destroy');

    /*
    |--------------------------------------------------------------------------
    | Wishlist
    |--------------------------------------------------------------------------
    */

    Route::get('/account/wishlist', [WishlistController::class, 'index'])
        ->name('account.wishlist.index');

    Route::post('/account/wishlist', [WishlistController::class, 'store'])
        ->name('account.wishlist.store');

    Route::post('/account/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])
        ->name('account.wishlist.toggle');

    Route::delete('/account/wishlist/{wishlist}', [WishlistController::class, 'destroy'])
        ->name('account.wishlist.destroy');

    /*
    |--------------------------------------------------------------------------
    | Customer Returns
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/account/returns',
        [CustomerReturnController::class, 'index']
    )->name('account.returns.index');

    Route::get(
        '/account/orders/{order}/return',
        [CustomerReturnController::class, 'create']
    )->name('account.returns.create');

    Route::post(
        '/account/orders/{order}/return',
        [CustomerReturnController::class, 'store']
    )->name('account.returns.store');

    Route::get(
        '/account/returns/{returnRequest}',
        [CustomerReturnController::class, 'show']
    )->name('account.returns.show');

    /*
    |--------------------------------------------------------------------------
    | Product Reviews
    |--------------------------------------------------------------------------
    */

    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])
        ->name('reviews.store');

    Route::put('/reviews/{review}', [ProductReviewController::class, 'update'])
        ->name('reviews.update');

    Route::delete('/reviews/{review}', [ProductReviewController::class, 'destroy'])
        ->name('reviews.destroy');
});

/*
|--------------------------------------------------------------------------
| Store Information Pages
|--------------------------------------------------------------------------
*/

Route::get('/about', function () {
    return view(
        'pages.info',
        ['page' => 'about']
    );
})->name('about');

Route::get('/contact', function () {
    return view(
        'pages.info',
        ['page' => 'contact']
    );
})->name('contact');

Route::get('/shipping', function () {
    return view(
        'pages.info',
        ['page' => 'shipping']
    );
})->name('shipping');

Route::get('/returns', function () {
    return view(
        'pages.info',
        ['page' => 'returns']
    );
})->name('returns');

/*
|--------------------------------------------------------------------------
| Order Success + Invoice
|--------------------------------------------------------------------------
*/

Route::get('/order-success', function () {

    $orderId = session('order_id');

    abort_unless($orderId, 404);

    $order = \App\Models\Order::findOrFail($orderId);

    return view('orders.success', compact('order'));

})->name('orders.success');

Route::get('/orders/{order}/invoice', [InvoiceController::class, 'show'])
    ->middleware('auth')
    ->name('orders.invoice');

/*
|--------------------------------------------------------------------------
| Admin + Staff Area
|--------------------------------------------------------------------------
|
| admin:
| - كامل لوحة الإدارة
|
| staff:
| - لوحة التحكم
| - الطلبات
| - استيراد المنتجات
| - المخزون
| - نقطة البيع
| - العملاء
|
*/

Route::middleware([
    'auth',
    'role:admin,staff',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Products Import
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/products/import',
        [ProductImportController::class, 'create']
    )->name('admin.products.import.form');

    Route::get(
        '/admin/products/import/template',
        [ProductImportController::class, 'downloadTemplate']
    )->name('admin.products.import.template');

    Route::post(
        '/admin/products/import',
        [ProductImportController::class, 'store']
    )->name('admin.products.import');

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/orders', [OrderController::class, 'index'])
        ->name('admin.orders.index');

    Route::patch(
        '/admin/orders/{order}/status',
        [OrderController::class, 'updateStatus']
    )->name('admin.orders.status.update');

    /*
    |--------------------------------------------------------------------------
    | Inventory
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/inventory',
        [InventoryController::class, 'index']
    )->name('admin.inventory.index');

    Route::post(
        '/admin/inventory/movements',
        [InventoryController::class, 'storeMovement']
    )->name('admin.inventory.movements.store');

    /*
    |--------------------------------------------------------------------------
    | Suppliers
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/suppliers',
        [SupplierController::class, 'index']
    )->name('admin.suppliers.index');

    Route::post(
        '/admin/suppliers',
        [SupplierController::class, 'store']
    )->name('admin.suppliers.store');

    Route::put(
        '/admin/suppliers/{supplier}',
        [SupplierController::class, 'update']
    )->name('admin.suppliers.update');

    Route::patch(
        '/admin/suppliers/{supplier}/toggle',
        [SupplierController::class, 'toggle']
    )->name('admin.suppliers.toggle');

    Route::get(
        '/admin/suppliers/{supplier}',
        [SupplierController::class, 'show']
    )->name('admin.suppliers.show');

    Route::post(
        '/admin/suppliers/{supplier}/payments',
        [SupplierPaymentController::class, 'store']
    )->name('admin.suppliers.payments.store');

    /*
    |--------------------------------------------------------------------------
    | Purchase Orders
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/purchases',
        [PurchaseOrderController::class, 'index']
    )->name('admin.purchases.index');

    Route::post(
        '/admin/purchases',
        [PurchaseOrderController::class, 'store']
    )->name('admin.purchases.store');

    Route::get(
        '/admin/purchases/{purchase}',
        [PurchaseOrderController::class, 'show']
    )->name('admin.purchases.show');

    Route::patch(
        '/admin/purchases/{purchase}/status',
        [PurchaseOrderController::class, 'updateStatus']
    )->name('admin.purchases.status');

    Route::patch(
        '/admin/purchases/{purchase}/receive',
        [PurchaseOrderController::class, 'receive']
    )->name('admin.purchases.receive');

    Route::patch(
        '/admin/purchases/{purchase}/payment',
        [PurchaseOrderController::class, 'updatePayment']
    )->name('admin.purchases.payment');

    /*
    |--------------------------------------------------------------------------
    | Advanced Inventory: Transfers
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/inventory/transfers',
        [StockTransferController::class, 'index']
    )->name('admin.inventory.transfers.index');

    Route::post(
        '/admin/inventory/transfers',
        [StockTransferController::class, 'store']
    )->name('admin.inventory.transfers.store');

    /*
    |--------------------------------------------------------------------------
    | Advanced Inventory: Stock Counts
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/inventory/counts',
        [StockCountController::class, 'index']
    )->name('admin.inventory.counts.index');

    Route::post(
        '/admin/inventory/counts',
        [StockCountController::class, 'store']
    )->name('admin.inventory.counts.store');

    Route::get(
        '/admin/inventory/counts/{stockCount}',
        [StockCountController::class, 'show']
    )->name('admin.inventory.counts.show');

    Route::patch(
        '/admin/inventory/counts/{stockCount}/complete',
        [StockCountController::class, 'complete']
    )->name('admin.inventory.counts.complete');

    /*
    |--------------------------------------------------------------------------
    | Advanced Inventory: Warehouses
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/inventory/warehouses',
        [WarehouseController::class, 'index']
    )->name('admin.inventory.warehouses.index');

    Route::post(
        '/admin/inventory/warehouses',
        [WarehouseController::class, 'store']
    )->name('admin.inventory.warehouses.store');

    Route::patch(
        '/admin/inventory/warehouses/{warehouse}/toggle',
        [WarehouseController::class, 'toggle']
    )->name('admin.inventory.warehouses.toggle');

    Route::patch(
        '/admin/inventory/warehouses/{warehouse}/default',
        [WarehouseController::class, 'makeDefault']
    )->name('admin.inventory.warehouses.default');

    /*
    |--------------------------------------------------------------------------
    | Advanced Inventory: Stock Management
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/inventory/stock-management',
        [AdvancedStockController::class, 'index']
    )->name('admin.inventory.stock.management');

    Route::patch(
        '/admin/inventory/stock-management/{stock}/settings',
        [AdvancedStockController::class, 'updateSettings']
    )->name('admin.inventory.stock.settings');

    Route::patch(
        '/admin/inventory/stock-management/{stock}/special',
        [AdvancedStockController::class, 'adjustSpecialStock']
    )->name('admin.inventory.stock.special');

    /*
    |--------------------------------------------------------------------------
    | Advanced Inventory: Supplier Returns
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/inventory/supplier-returns',
        [SupplierReturnController::class, 'index']
    )->name('admin.inventory.supplier-returns.index');

    Route::post(
        '/admin/inventory/supplier-returns',
        [SupplierReturnController::class, 'store']
    )->name('admin.inventory.supplier-returns.store');

    /*
    |--------------------------------------------------------------------------
    | Advanced Inventory: Barcodes & Labels
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/inventory/barcodes',
        [BarcodeController::class, 'index']
    )->name('admin.inventory.barcodes.index');

    /*
    |--------------------------------------------------------------------------
    | Advanced Inventory: Reorder Alerts
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/inventory/alerts',
        [InventoryAlertController::class, 'index']
    )->name('admin.inventory.alerts.index');

    /*
    |--------------------------------------------------------------------------
    | POS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/pos', [PosController::class, 'index'])
        ->name('admin.pos.index');

    Route::post('/admin/pos', [PosController::class, 'store'])
        ->name('admin.pos.store');

    /*
    |--------------------------------------------------------------------------
    | Professional POS Operations
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/pos/customers/search',
        [PosOperationsController::class, 'customers']
    )->name('admin.pos.customers.search');

    Route::get(
        '/admin/pos/sales/history',
        [PosOperationsController::class, 'salesHistory']
    )->name('admin.pos.sales.history');

    Route::get(
        '/admin/pos/sales/{sale}/details',
        [PosOperationsController::class, 'saleDetails']
    )->name('admin.pos.sales.details');

    Route::get(
        '/admin/pos/held-sales',
        [PosOperationsController::class, 'heldSales']
    )->name('admin.pos.held.index');

    Route::post(
        '/admin/pos/held-sales',
        [PosOperationsController::class, 'holdSale']
    )->name('admin.pos.held.store');

    Route::post(
        '/admin/pos/held-sales/{heldSale}/resume',
        [PosOperationsController::class, 'resumeHeldSale']
    )->name('admin.pos.held.resume');

    Route::delete(
        '/admin/pos/held-sales/{heldSale}',
        [PosOperationsController::class, 'deleteHeldSale']
    )->name('admin.pos.held.destroy');

    Route::post(
        '/admin/pos/sales/{sale}/return',
        [PosOperationsController::class, 'createReturn']
    )->name('admin.pos.returns.store');

    Route::get(
        '/admin/pos/sales/{sale}/receipt',
        [PosOperationsController::class, 'receipt']
    )->name('admin.pos.receipt');

    Route::post(
        '/admin/pos/sales/{sale}/receipt/print',
        [PosOperationsController::class, 'printReceipt']
    )->name('admin.pos.receipt.print');

    Route::post(
        '/admin/pos/drawer/open',
        [PosOperationsController::class, 'openDrawer']
    )->name('admin.pos.drawer.open');

    Route::post(
        '/admin/pos/drawer/cash-in',
        [PosOperationsController::class, 'cashIn']
    )->name('admin.pos.drawer.cash-in');

    Route::post(
        '/admin/pos/drawer/cash-out',
        [PosOperationsController::class, 'cashOut']
    )->name('admin.pos.drawer.cash-out');

    /*
    |--------------------------------------------------------------------------
    | POS Shifts
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/pos/shifts',
        [PosShiftController::class, 'index']
    )->name('admin.pos.shifts.index');

    Route::post(
        '/admin/pos/shifts/open',
        [PosShiftController::class, 'open']
    )->name('admin.pos.shifts.open');

    Route::post(
        '/admin/pos/shifts/{shift}/close',
        [PosShiftController::class, 'close']
    )->name('admin.pos.shifts.close');

    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/customers', [CustomerController::class, 'index'])
        ->name('admin.customers.index');

    Route::get('/admin/customers/{customer}', [CustomerController::class, 'show'])
        ->name('admin.customers.show');

    Route::patch(
        '/admin/customers/{customer}/toggle',
        [CustomerController::class, 'toggle']
    )->name('admin.customers.toggle');
});

/*
|--------------------------------------------------------------------------
| Admin Only Area
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:admin',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Coupons
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/coupons', [CouponController::class, 'index'])
        ->name('admin.coupons.index');

    Route::post('/admin/coupons', [CouponController::class, 'store'])
        ->name('admin.coupons.store');

    Route::put('/admin/coupons/{coupon}', [CouponController::class, 'update'])
        ->name('admin.coupons.update');

    Route::patch(
        '/admin/coupons/{coupon}/toggle',
        [CouponController::class, 'toggle']
    )->name('admin.coupons.toggle');

    Route::delete(
        '/admin/coupons/{coupon}',
        [CouponController::class, 'destroy']
    )->name('admin.coupons.destroy');

    /*
    |--------------------------------------------------------------------------
    | Product Reviews
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/reviews',
        [ReviewController::class, 'index']
    )->name('admin.reviews.index');

    Route::patch(
        '/admin/reviews/{review}/approve',
        [ReviewController::class, 'approve']
    )->name('admin.reviews.approve');

    Route::patch(
        '/admin/reviews/{review}/hide',
        [ReviewController::class, 'hide']
    )->name('admin.reviews.hide');

    Route::patch(
        '/admin/reviews/{review}/show',
        [ReviewController::class, 'showAgain']
    )->name('admin.reviews.show');

    Route::patch(
        '/admin/reviews/{review}/reject',
        [ReviewController::class, 'reject']
    )->name('admin.reviews.reject');

    Route::delete(
        '/admin/reviews/{review}',
        [ReviewController::class, 'destroy']
    )->name('admin.reviews.destroy');

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/reports', [ReportController::class, 'index'])
        ->name('admin.reports.index');

    /*
    |--------------------------------------------------------------------------
    | Cash Registers
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/cash-registers',
        [CashRegisterController::class, 'index']
    )->name('admin.cash-registers.index');

    Route::post(
        '/admin/cash-registers',
        [CashRegisterController::class, 'store']
    )->name('admin.cash-registers.store');

    Route::patch(
        '/admin/cash-registers/{cashRegister}/toggle',
        [CashRegisterController::class, 'toggle']
    )->name('admin.cash-registers.toggle');

    Route::post(
        '/admin/cash-registers/{cashRegister}/movement',
        [CashRegisterController::class, 'movement']
    )->name('admin.cash-registers.movement');

    /*
    |--------------------------------------------------------------------------
    | Expenses
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/expenses', [ExpenseController::class, 'index'])
        ->name('admin.expenses.index');

    Route::post('/admin/expenses', [ExpenseController::class, 'store'])
        ->name('admin.expenses.store');

    Route::delete(
        '/admin/expenses/{expense}',
        [ExpenseController::class, 'destroy']
    )->name('admin.expenses.destroy');

    /*
    |--------------------------------------------------------------------------
    | Staff + Permissions
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/staff', [StaffController::class, 'index'])
        ->name('admin.staff.index');

    Route::post('/admin/staff', [StaffController::class, 'store'])
        ->name('admin.staff.store');

    Route::patch('/admin/staff/{user}', [StaffController::class, 'update'])
        ->name('admin.staff.update');

    Route::patch(
        '/admin/staff/{user}/password',
        [StaffController::class, 'updatePassword']
    )->name('admin.staff.password');

    Route::patch(
        '/admin/staff/{user}/toggle',
        [StaffController::class, 'toggle']
    )->name('admin.staff.toggle');

    /*
    |--------------------------------------------------------------------------
    | Marketing
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Marketing + Loyalty + Smart Commerce
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/marketing',
        [MarketingController::class, 'index']
    )->name('admin.marketing.index');

    Route::post(
        '/admin/marketing/promotions',
        [MarketingController::class, 'storePromotion']
    )->name('admin.marketing.promotions.store');

    Route::post(
        '/admin/marketing/segments',
        [MarketingController::class, 'storeSegment']
    )->name('admin.marketing.segments.store');

    Route::post(
        '/admin/marketing/campaigns',
        [MarketingController::class, 'storeCampaign']
    )->name('admin.marketing.campaigns.store');

    Route::get(
        '/admin/marketing/bundles',
        [MarketingController::class, 'bundles']
    )->name('admin.marketing.bundles.index');

    Route::post(
        '/admin/marketing/bundles',
        [MarketingController::class, 'storeBundle']
    )->name('admin.marketing.bundles.store');

    Route::get(
        '/admin/marketing/loyalty',
        [LoyaltyAdminController::class, 'index']
    )->name('admin.marketing.loyalty.index');

    Route::post(
        '/admin/marketing/loyalty/{user}/profile',
        [LoyaltyAdminController::class, 'ensureProfile']
    )->name('admin.marketing.loyalty.profile');

    Route::post(
        '/admin/marketing/loyalty/{user}/points',
        [LoyaltyAdminController::class, 'addPoints']
    )->name('admin.marketing.loyalty.points');

    Route::post(
        '/admin/marketing/loyalty/{user}/store-credit',
        [LoyaltyAdminController::class, 'addStoreCredit']
    )->name('admin.marketing.loyalty.store-credit');

    Route::get(
        '/admin/marketing/gift-cards',
        [GiftCardController::class, 'index']
    )->name('admin.marketing.gift-cards.index');

    Route::post(
        '/admin/marketing/gift-cards',
        [GiftCardController::class, 'store']
    )->name('admin.marketing.gift-cards.store');

    /*
    |--------------------------------------------------------------------------
    | Finance + Employees + Payroll
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/finance',
        [FinanceController::class, 'index']
    )->name('admin.finance.index');

    Route::post(
        '/admin/finance/accounts',
        [FinanceController::class, 'storeAccount']
    )->name('admin.finance.accounts.store');

    Route::post(
        '/admin/finance/transactions',
        [FinanceController::class, 'storeTransaction']
    )->name('admin.finance.transactions.store');

    Route::get(
        '/admin/finance/employees',
        [EmployeeFinanceController::class, 'index']
    )->name('admin.finance.employees.index');

    Route::post(
        '/admin/finance/employees',
        [EmployeeFinanceController::class, 'store']
    )->name('admin.finance.employees.store');

    Route::get(
        '/admin/finance/employees/{employee}',
        [EmployeeFinanceController::class, 'show']
    )->name('admin.finance.employees.show');

    Route::put(
        '/admin/finance/employees/{employee}',
        [EmployeeFinanceController::class, 'update']
    )->name('admin.finance.employees.update');

    Route::post(
        '/admin/finance/employees/{employee}/advances',
        [EmployeeFinanceController::class, 'addAdvance']
    )->name('admin.finance.employees.advances.store');

    Route::post(
        '/admin/finance/employees/{employee}/adjustments',
        [EmployeeFinanceController::class, 'addAdjustment']
    )->name('admin.finance.employees.adjustments.store');

    Route::get(
        '/admin/finance/payroll',
        [PayrollController::class, 'index']
    )->name('admin.finance.payroll.index');

    Route::post(
        '/admin/finance/payroll',
        [PayrollController::class, 'store']
    )->name('admin.finance.payroll.store');

    Route::get(
        '/admin/finance/payroll/{period}',
        [PayrollController::class, 'show']
    )->name('admin.finance.payroll.show');

    Route::post(
        '/admin/finance/payroll/{period}/calculate',
        [PayrollController::class, 'calculate']
    )->name('admin.finance.payroll.calculate');

    Route::post(
        '/admin/finance/payroll/{period}/approve',
        [PayrollController::class, 'approve']
    )->name('admin.finance.payroll.approve');

    Route::post(
        '/admin/finance/payroll/{period}/pay',
        [PayrollController::class, 'pay']
    )->name('admin.finance.payroll.pay');

    /*
    |--------------------------------------------------------------------------
    | Shipping + Payments + Returns
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/shipping',
        [ShippingAdminController::class, 'index']
    )->name('admin.shipping.index');

    Route::post(
        '/admin/shipping/carriers',
        [ShippingAdminController::class, 'storeCarrier']
    )->name('admin.shipping.carriers.store');

    Route::post(
        '/admin/shipping/services',
        [ShippingAdminController::class, 'storeService']
    )->name('admin.shipping.services.store');

    Route::post(
        '/admin/shipping/zones',
        [ShippingAdminController::class, 'storeZone']
    )->name('admin.shipping.zones.store');

    Route::post(
        '/admin/shipping/rates',
        [ShippingAdminController::class, 'storeRate']
    )->name('admin.shipping.rates.store');

    Route::post(
        '/admin/orders/{order}/shipments',
        [ShippingAdminController::class, 'createShipment']
    )->name('admin.shipping.shipments.store');

    Route::post(
        '/admin/shipments/{shipment}/status',
        [ShippingAdminController::class, 'updateShipmentStatus']
    )->name('admin.shipping.shipments.status');

    Route::get(
        '/admin/payments',
        [PaymentAdminController::class, 'index']
    )->name('admin.payments.index');

    Route::post(
        '/admin/payments/gateways',
        [PaymentAdminController::class, 'storeGateway']
    )->name('admin.payments.gateways.store');

    Route::get(
        '/admin/returns',
        [ReturnAdminController::class, 'index']
    )->name('admin.returns.index');

    Route::get(
        '/admin/returns/{returnRequest}',
        [ReturnAdminController::class, 'show']
    )->name('admin.returns.show');

    Route::post(
        '/admin/returns/{returnRequest}/approve',
        [ReturnAdminController::class, 'approve']
    )->name('admin.returns.approve');

    Route::post(
        '/admin/returns/{returnRequest}/received',
        [ReturnAdminController::class, 'markReceived']
    )->name('admin.returns.received');

    Route::post(
        '/admin/returns/{returnRequest}/refund',
        [ReturnAdminController::class, 'refund']
    )->name('admin.returns.refund');

    Route::post(
        '/admin/returns/policies',
        [ReturnAdminController::class, 'storePolicy']
    )->name('admin.returns.policies.store');

    /*
    |--------------------------------------------------------------------------
    | Administration + Reports + Security
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/security',
        [SecurityAdminController::class, 'index']
    )->name('admin.security.index');

    Route::patch(
        '/admin/security/users/{user}/permissions/{permission}',
        [SecurityAdminController::class, 'setUserPermission']
    )->name('admin.security.user-permissions.update');

    Route::patch(
        '/admin/security/features/{featureFlag}',
        [SecurityAdminController::class, 'updateFeatureFlag']
    )->name('admin.security.features.update');

    Route::get(
        '/admin/approvals',
        [ApprovalAdminController::class, 'index']
    )->name('admin.approvals.index');

    Route::post(
        '/admin/approvals/{approvalRequest}/review',
        [ApprovalAdminController::class, 'review']
    )->name('admin.approvals.review');

    Route::get(
        '/admin/health',
        [HealthAdminController::class, 'index']
    )->name('admin.health.index');

    Route::post(
        '/admin/health/run',
        [HealthAdminController::class, 'runChecks']
    )->name('admin.health.run');

    Route::post(
        '/admin/health/incidents',
        [HealthAdminController::class, 'storeIncident']
    )->name('admin.health.incidents.store');

    Route::post(
        '/admin/health/incidents/{incident}/resolve',
        [HealthAdminController::class, 'resolveIncident']
    )->name('admin.health.incidents.resolve');

    Route::get(
        '/admin/report-center',
        [ReportsAdminController::class, 'index']
    )->name('admin.report-center.index');

    Route::post(
        '/admin/report-center',
        [ReportsAdminController::class, 'store']
    )->name('admin.report-center.store');

    Route::post(
        '/admin/report-center/{savedReport}/schedule',
        [ReportsAdminController::class, 'schedule']
    )->name('admin.report-center.schedule');

    Route::get(
        '/admin/search',
        [AdminSearchController::class, 'index']
    )->name('admin.search.index');

    Route::get(
        '/admin/backups',
        [BackupAdminController::class, 'index']
    )->name('admin.backups.index');

    Route::post(
        '/admin/backups',
        [BackupAdminController::class, 'requestBackup']
    )->name('admin.backups.store');

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/settings', function () {
        return view('admin.settings.index');
    })->name('admin.settings.index');
});
