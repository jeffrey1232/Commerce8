<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ColisController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CommunityManagerController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

// Page d'accueil redirigée vers la connexion multi-rôles
Route::get('/', function () {
    return redirect('/login-multi-role');
});

// Page de connexion multi-rôles
Route::get('/login-multi-role', function () {
    return view('auth.login-multi-role');
});

// Routes des interfaces frontend avec vrais controllers
Route::get('/admin-dashboard', [AdminController::class, 'index']);
Route::get('/vendor-portal', [VendorController::class, 'index'])->middleware('auth')->name('vendor.portal');
Route::get('/community-manager', [CommunityManagerController::class, 'index'])->middleware('auth');
Route::get('/client-validation', [ClientController::class, 'index']);

// Routes API pour les actions frontend
Route::post('/validate-code', [ClientController::class, 'validateCode']);
Route::post('/process-payment', [ClientController::class, 'processPayment']);
Route::post('/download-receipt/{receiptNumber}', [ClientController::class, 'downloadReceipt']);
Route::get('/check-fitting/{colisUuid}', [ClientController::class, 'checkFittingStatus']);

Route::post('/vendor/create-shipment', [VendorController::class, 'store'])->middleware('auth');
Route::get('/vendor/create', [VendorController::class, 'create'])->middleware('auth');
Route::get('/vendor/{uuid}', [VendorController::class, 'show'])->middleware('auth');

Route::post('/admin/approve-disbursement/{vendorId}', [AdminController::class, 'approveDisbursement'])->middleware('auth');
Route::get('/admin/stats', [AdminController::class, 'getStats'])->middleware('auth');

Route::post('/community/create-shooting', [CommunityManagerController::class, 'createShooting'])->middleware('auth');
Route::post('/community/update-production', [CommunityManagerController::class, 'updateProductionStatus'])->middleware('auth');
Route::get('/community/metrics', [CommunityManagerController::class, 'getPerformanceMetrics'])->middleware('auth');

// Routes Laravel existantes (avec authentification)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Routes pour le dashboard
    Route::get('/api/overdue-colis', [DashboardController::class, 'getOverdueColis']);
    Route::get('/api/vendor-stats', [DashboardController::class, 'getVendorStats']);
    Route::get('/api/payment-stats', [DashboardController::class, 'getPaymentStats']);
    Route::get('/api/cabin-stats', [DashboardController::class, 'getCabinStats']);
});

// Routes pour les colis
Route::resource('colis', ColisController::class)->middleware(['auth', 'verified']);
Route::post('/colis/{uuid}/deposit', [ColisController::class, 'deposit'])->middleware('auth');
Route::post('/colis/{uuid}/withdraw', [ColisController::class, 'withdraw'])->middleware('auth');
Route::post('/colis/{uuid}/status', [ColisController::class, 'updateStatus'])->middleware('auth');
Route::get('/colis/{uuid}/payment', [ColisController::class, 'payment'])->middleware('auth');
Route::post('/colis/{uuid}/payment', [ColisController::class, 'processPayment'])->middleware('auth');

Route::resource('products', ProductController::class);

require __DIR__.'/auth.php';
