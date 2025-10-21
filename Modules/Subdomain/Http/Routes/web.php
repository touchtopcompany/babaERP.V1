<?php

use Illuminate\Support\Facades\Route;
use Modules\Subdomain\Http\Controllers\SubdomainController;

Route::middleware(['web', 'auth', 'language', 'AdminSidebarMenu', 'superadmin'])->group(function () {

    Route::controller(SubdomainController::class)->prefix('subdomains')->group(function () {
        Route::get('add-subscription', 'addSubscription')->name('add-subscription');
        Route::post('add-subscription', 'saveSubscription');


        Route::put('enable-disable-domain/{subdomain}', 'enableDisableSubdomainAccount');
    });

    Route::resource('subdomains', SubdomainController::class);
});