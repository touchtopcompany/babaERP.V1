<?php

use App\Http\Controllers\SubdomainController;
use Illuminate\Support\Facades\Route;


Route::controller(SubdomainController::class)->middleware(['superadmin'])->prefix('subdomain')->group(function () {
    Route::get('/registered', 'getSubDomains')->name('registered-subdomains');
    Route::get('/create-subdomain', 'createSubDomain');
    Route::post('/create-subdomain', 'postSubDomain');
    Route::get('/edit-subdomain/{id}', 'editSubDomain');
    Route::put('/update-subdomain/{id}', 'updateSubDomain');
    Route::delete('/delete-subdomain/{id}', 'deleteSubDomain');

    //Auto Login
});