
<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/status', function () {
        return response()->json([
            'success' => true,
            'service' => 'lamset-anotha-api',
            'version' => 'v1',
        ]);
    })->name('api.v1.status');
});
