<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserPushTokenController;
use App\Http\Controllers\PaymentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/send-test-notification', [UserPushTokenController::class, 'send']);
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->post('/api/user-push-token', [UserPushTokenController::class, 'store'])->name('api.user-push-token.store');


// ---------------------------
// Setting Routes
// ---------------------------

Route::get('/settings/tariffs', [AdminController::class, 'getTariffSettings'])->name('settings.tariffs.get');
Route::get('/neshan' , [TripController::class, 'neshanAPI'])->name('neshan');

// ---------------------------
// page Route
// ---------------------------      

Route::get('/', [UserController::class, 'home'])->name('home');

// ---------------------------
// Public / User Routes
// ---------------------------

// Login routes
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'loginVerification'])->name('login.verify');
Route::post('/login/verify-code', [UserController::class, 'checkVerification'])->name('login.code.verify');

// Logout route (POST)
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');

// Debug route to read cached verification code (only in debug mode)
Route::get('/debug/verification-code', [UserController::class, 'debugCachedCode'])->name('debug.verification.code');


// Profile update route (handles phone-change verification or immediate save)
Route::post('/profile/update', [UserController::class, 'updateProfile'])
    ->middleware('auth')
    ->name('profile.update');
// Driver registration route
Route::post('/driver/save', [UserController::class, 'driverSave'])
    ->middleware('auth')
    ->name('driver.save');
// Trip status page (temporary view)
Route::get('trip-status/{id}', function () {
    return view('user-state-acceped');
})->name('user.trip-status');

// Test helper
Route::get('/test-helper', function () {
    return setting();
});

// Trips store route (middleware: isPassenger)

Route::post('/trips/store', [TripController::class, 'store'])
    ->middleware(['isPassenger'])
    ->name('trips.store');

Route::get('/driver/trips', [TripController::class, 'index'])->name('driver.trips');
Route::middleware(['auth:admin'])->get('/trips/assign-driver', [AdminController::class, 'assignDriver'])->name('trips.assign-driver');
Route::get('trip/payment', [TripController::class,'payment'])->name('trip.payment');

// ---------------------------
// Payment
// ---------------------------
Route::get('payment/verify',[PaymentController::class, 'verify'])->name('payment.verify');

// ---------------------------
// Admin Routes
// ---------------------------
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
})->name('admin');

Route::prefix('admin')->group(function () {

    //َ Login routes
    Route::get('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'loginVerification'])->name('admin.login.verify');

    // Routes protected by admin authentication
    Route::middleware('auth:admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Logout
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

        // Pricing update
        Route::post('/pricing/update', [AdminController::class, 'updatePricing'])->name('admin.pricing.update');

        // Car Types 
        Route::post('/car-types/save', [AdminController::class, 'saveCarType'])->name('admin.car-types.save');
        Route::post('/car-types/delete', [AdminController::class, 'deleteCarType'])->name('admin.car-types.delete');

        // Cars
        Route::post('/cars/save', [AdminController::class, 'saveCar'])->name('admin.cars.save');
        Route::post('/cars/delete', [AdminController::class, 'deleteCar'])->name('admin.cars.delete');

        // Admins 
        Route::post('/admins/save', [AdminController::class, 'saveAdmin'])->name('admin.admins.save');
        Route::post('/admins/delete', [AdminController::class, 'deleteAdmin'])->name('admin.admins.delete');

        // Zones
        Route::post('/zones/save', [AdminController::class, 'saveZone'])->name('admin.zones.save');
        Route::post('/zones/delete', [AdminController::class, 'deleteZone'])->name('admin.zones.delete');

        // Settings
        Route::post('/settings/save', [AdminController::class, 'saveSettings'])->name('admin.settings.save');

        // Users
        Route::post('/users/save', [AdminController::class, 'saveUser'])->name('admin.users.save');
        Route::post('/users/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

        // Drivers
        Route::post('/drivers/delete', [AdminController::class, 'deleteDriver'])->name('admin.drivers.delete');
        Route::post('/drivers/deactivate', [AdminController::class, 'toggleStatus'])->name('admin.drivers.toggle.status');
        Route::post('/drivers/save', [AdminController::class, 'saveDriver'])->name( 'admin.drivers.save');
        Route::get('/drivers/search', [AdminController::class, 'searchDriver'])->name('admin.drivers.search');

        // Driver documents 
        Route::post('/drivers/documents/approve', [AdminController::class, 'approveDriverDocument'])->name( 'admin.drivers.documents.approve');
        Route::post('/drivers/documents/reject', [AdminController::class, 'rejectDriverDocument'])->name('admin.drivers.documents.reject');
        
        // Trips
        Route::post('/trips/cancel', [AdminController::class, 'cancelTrip'])->name('admin.trips.cancel');
        
        // Load admin pages via iframe
        Route::get('/page/{slug}', [AdminController::class, 'loadAdminPage'])->name('admin.page');
    });
});
