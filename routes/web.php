<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {

});

Route::group([
    'prefix' => 'v1',
], function () {
    Route::get('orders', [\App\Http\Controllers\Api::class, 'orders'])->name('get-orders');

    // TODO 4 fonksiyon implement edilecek. Gerekli yerlerde Request/Resource class'ları üstteki endpointteki şekliyle kullanılmalıdır.

    Route::get('product/{productId}', [\App\Http\Controllers\ProductController::class, 'get'])->name('get-product');
    Route::put('product/{productId}', [\App\Http\Controllers\ProductController::class, 'update'])->name('update-product');
    Route::post('product', [\App\Http\Controllers\ProductController::class, 'store'])->name('create-product');
    Route::delete('product/{productId}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('destroy-product');

});
