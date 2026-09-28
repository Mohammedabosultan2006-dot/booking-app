
<?php

use App\Http\Controllers\{BookingController, PaymentController, ReviewController, ServiceController};
use App\Http\Controllers\Provider;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('services.index'));


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/services',          [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}',[ServiceController::class, 'show'])->name('services.show');

// ─── Auth (Breeze) ─────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ─── Customer ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/bookings',              [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/slots/{slot}', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings',             [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}',    [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/bookings/{booking}/payment',  [PaymentController::class, 'create'])->name('bookings.payment.create');
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store'])->name('bookings.payment.store');

    Route::post('/bookings/{booking}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// ─── Provider ──────────────────────────────────────────────────────
Route::middleware(['auth', 'role:provider,admin'])
    ->prefix('provider')
    ->name('provider.')
    ->group(function () {
        // Dashboard
        Route::get('/', [Provider\DashboardController::class, 'index'])->name('dashboard');

        // Services
        Route::resource('services', Provider\ServiceController::class);

        // Slots
        Route::get('services/{service}/slots',          [Provider\SlotController::class, 'index'])->name('slots.index');
        Route::post('services/{service}/slots',         [Provider\SlotController::class, 'store'])->name('slots.store');
        Route::post('services/{service}/slots/bulk',    [Provider\SlotController::class, 'bulkStore'])->name('slots.bulk');
        Route::delete('slots/{slot}',                   [Provider\SlotController::class, 'destroy'])->name('slots.destroy');

        // Bookings
        Route::get('bookings',                          [Provider\BookingController::class, 'index'])->name('bookings.index');
        Route::patch('bookings/{booking}/confirm',      [Provider\BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::patch('bookings/{booking}/cancel',       [Provider\BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::patch('bookings/{booking}/complete',     [Provider\BookingController::class, 'complete'])->name('bookings.complete');

        // Payment methods
        Route::resource('payment-methods', Provider\PaymentMethodController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Payment verification
        Route::patch('payments/{payment}/verify', [Provider\PaymentController::class, 'verify'])->name('payments.verify');
        Route::patch('payments/{payment}/reject', [Provider\PaymentController::class, 'reject'])->name('payments.reject');
    });

// ─── Admin ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/',                              [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/users',                         [UserController::class, 'index'])->name('users');
        Route::get('/users/create',                  [UserController::class, 'create'])->name('users.create');
        Route::post('/users',                        [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',              [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',                   [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/role',           [UserController::class, 'updateRole'])->name('users.role');
        Route::delete('/users/{user}',               [UserController::class, 'destroy'])->name('users.destroy');
    });
