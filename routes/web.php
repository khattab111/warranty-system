<?php

use App\Http\Controllers\PublicWarrantyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/warranty/{public_token}', [PublicWarrantyController::class, 'show'])
        ->name('warranty.show')
        ->where('public_token', '[a-f0-9\-]+');

    Route::get('/warranty/{warranty:public_token}/qr/download', [PublicWarrantyController::class, 'downloadQr'])
        ->name('warranty.qr.download');

    Route::get('/warranty/{warranty:public_token}/print', [PublicWarrantyController::class, 'printWarranty'])
        ->name('warranty.print');
});
