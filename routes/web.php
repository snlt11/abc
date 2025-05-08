<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantDomainController;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', [TenantDomainController::class, 'index']);
        Route::post('/', [TenantDomainController::class, 'checkDomain']);      
    });
}
