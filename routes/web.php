<?php

use App\Http\Controllers\SalonController;
use Illuminate\Support\Facades\Route;

Route::name('salon.')->group(function () {
    Route::redirect('/', '/dashboard', 302);

    Route::get('login', [SalonController::class, 'showLogin'])->name('login');
    Route::post('login', [SalonController::class, 'loginPost'])->name('login.post');
    Route::get('logout', [SalonController::class, 'logout'])->name('logout');
    Route::get('forgot-password', [SalonController::class, 'showForgotPassword'])->name('forgot-password');

    Route::get('dashboard', [SalonController::class, 'dashboard'])->name('dashboard');

    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [SalonController::class, 'booking'])->name('index');
        Route::get('calendar', [SalonController::class, 'calendar'])->name('calendar');
        Route::get('create-ticket', [SalonController::class, 'createTicket'])->name('create-ticket');
        Route::get('edit-booking', [SalonController::class, 'editBooking'])->name('edit-booking');
        Route::get('pay', [SalonController::class, 'pay'])->name('pay');
        Route::get('tickets', [SalonController::class, 'tickets'])->name('tickets');
        Route::get('waiting-list', [SalonController::class, 'waitingList'])->name('waiting-list');
    });

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [SalonController::class, 'customersIndex'])->name('index');
        Route::get('view', [SalonController::class, 'customersView'])->name('view');
    });

    Route::get('services', [SalonController::class, 'servicesIndex'])->name('services.index');

    Route::prefix('technicians')->name('technicians.')->group(function () {
        Route::get('/', [SalonController::class, 'techniciansIndex'])->name('index');
        Route::get('view', [SalonController::class, 'techniciansView'])->name('view');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [SalonController::class, 'usersIndex'])->name('index');
        Route::get('view', [SalonController::class, 'usersView'])->name('view');
    });

    Route::get('payments', [SalonController::class, 'paymentsIndex'])->name('payments.index');
    Route::get('payout', [SalonController::class, 'payoutIndex'])->name('payout.index');
    Route::get('orders', [SalonController::class, 'ordersIndex'])->name('orders.index');
    Route::get('turn-tracker', [SalonController::class, 'turnTrackerIndex'])->name('turn-tracker.index');
    Route::get('settings', [SalonController::class, 'settingsIndex'])->name('settings.index');
    Route::get('profile', [SalonController::class, 'profileIndex'])->name('profile.index');
});
