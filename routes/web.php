<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\MultiRoleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\CommunityManager\StudioController;

// Page d'accueil redirigée vers la connexion multi-rôles
Route::get('/', function () {
    return redirect('/login-multi-role');
});

// Authentification multi-rôles
Route::get('/login-multi-role', [MultiRoleController::class, 'showLoginForm'])->name('login.multi');
Route::post('/login-multi-role', [MultiRoleController::class, 'login']);

// Routes Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/approve-disbursement/{paymentId}', [AdminController::class, 'approveDisbursement'])->name('approve.disbursement');
    Route::post('/generate-report', [AdminController::class, 'generateReport'])->name('generate.report');
    Route::get('/vendors', [AdminController::class, 'getVendors'])->name('vendors.index');
    Route::put('/vendors/{vendorId}/status', [AdminController::class, 'updateVendorStatus'])->name('vendors.update.status');
    Route::get('/packages', [AdminController::class, 'getPackages'])->name('packages.index');
    Route::put('/packages/{packageId}/status', [AdminController::class, 'updatePackageStatus'])->name('packages.update.status');
    Route::get('/payments', [AdminController::class, 'getPayments'])->name('payments.index');
});

// Routes Vendeur
Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
    Route::post('/create-shipment', [VendorController::class, 'createShipment'])->name('create.shipment');
    Route::get('/wallet', [VendorController::class, 'wallet'])->name('wallet');
    Route::get('/packages', [VendorController::class, 'packages'])->name('packages');
});

// Routes Client
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/validation', [ClientController::class, 'validation'])->name('validation');
    Route::post('/validate-code', [ClientController::class, 'validateCode'])->name('validate.code');
    Route::post('/process-payment', [ClientController::class, 'processPayment'])->name('process.payment');
});

// Routes Community Manager
Route::middleware(['auth', 'role:community_manager'])->prefix('studio')->name('studio.')->group(function () {
    Route::get('/dashboard', [StudioController::class, 'dashboard'])->name('dashboard');
    Route::get('/fitting-rooms', [StudioController::class, 'fittingRooms'])->name('fitting.rooms');
    Route::get('/shooting-slots', [StudioController::class, 'shootingSlots'])->name('shooting.slots');
});

// Routes API
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('/packages/status/{trackingCode}', [App\Http\Controllers\API\PackageAPIController::class, 'getStatus']);
    Route::post('/payments/verify', [App\Http\Controllers\API\PaymentAPIController::class, 'verify']);
    Route::get('/vendors/stats', [App\Http\Controllers\API\VendorAPIController::class, 'stats']);
});

// Routes statiques pour les vues existantes
Route::get('/admin-dashboard', function () {
    return view('layouts.glassmorphism');
});
Route::get('/vendor-portal', function () {
    return view('vendor.portal');
});
Route::get('/community-manager', function () {
    return view('community.manager');
});
Route::get('/client-validation', function () {
    return view('client.validation');
});

require __DIR__.'/auth.php';
