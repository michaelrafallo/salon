<?php

use App\Http\Controllers\SalonAppointmentController;
use App\Http\Controllers\SalonController;
use App\Http\Controllers\SalonCouponController;
use App\Http\Controllers\SalonCustomerController;
use App\Http\Controllers\SalonDataController;
use App\Http\Controllers\SalonGiftCardController;
use App\Http\Controllers\SalonPaymentController;
use App\Http\Controllers\SalonServiceController;
use App\Http\Controllers\SalonSettingsController;
use App\Http\Controllers\SalonTurnTrackerController;
use App\Http\Controllers\SalonUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'salon.auth'])->prefix('api/salon')->name('api.salon.')->group(function () {
    Route::get('profile', [SalonUserController::class, 'profile'])->name('profile');
    Route::put('profile', [SalonUserController::class, 'updateProfile'])->name('profile.update');
    Route::post('customers', [SalonCustomerController::class, 'store'])->name('customers.store');
    Route::put('customers/{customer}', [SalonCustomerController::class, 'update'])->name('customers.update');
    Route::post('customers/{customer}/credits', [SalonCustomerController::class, 'updateCredits'])->name('customers.credits.update');
    Route::delete('customers/{customer}', [SalonCustomerController::class, 'destroy'])->name('customers.destroy');
    Route::post('users', [SalonUserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [SalonUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [SalonUserController::class, 'destroy'])->name('users.destroy');
    Route::post('services', [SalonServiceController::class, 'store'])->name('services.store');
    Route::put('services/{service}', [SalonServiceController::class, 'update'])->name('services.update');
    Route::delete('services/{service}', [SalonServiceController::class, 'destroy'])->name('services.destroy');
    Route::post('payments', [SalonPaymentController::class, 'store'])->name('payments.store');
    Route::put('payments/{payment}', [SalonPaymentController::class, 'update'])->name('payments.update');
    Route::post('appointments', [SalonAppointmentController::class, 'store'])->name('appointments.store');
    Route::put('appointments/{appointment}', [SalonAppointmentController::class, 'update'])->name('appointments.update');
    Route::put('appointments/{appointment}/services', [SalonAppointmentController::class, 'updateServices'])->name('appointments.services.update');
    Route::delete('appointments/{appointment}', [SalonAppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::get('turn-tracker', [SalonTurnTrackerController::class, 'index'])->name('turn-tracker.index');
    Route::put('turn-tracker', [SalonTurnTrackerController::class, 'sync'])->name('turn-tracker.sync');
    Route::post('technicians/{user}/clock-in', [SalonTurnTrackerController::class, 'clockIn'])->name('technicians.clock-in');
    Route::post('technicians/{user}/clock-out', [SalonTurnTrackerController::class, 'clockOut'])->name('technicians.clock-out');
    Route::get('settings', [SalonSettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SalonSettingsController::class, 'update'])->name('settings.update');
    Route::get('coupons', [SalonCouponController::class, 'index'])->name('coupons.index');
    Route::post('coupons', [SalonCouponController::class, 'store'])->name('coupons.store');
    Route::put('coupons/{coupon}', [SalonCouponController::class, 'update'])->name('coupons.update');
    Route::delete('coupons/{coupon}', [SalonCouponController::class, 'destroy'])->name('coupons.destroy');
    Route::get('gift-cards', [SalonGiftCardController::class, 'index'])->name('gift-cards.index');
    Route::post('gift-cards', [SalonGiftCardController::class, 'store'])->name('gift-cards.store');
    Route::put('gift-cards/{gift_card}', [SalonGiftCardController::class, 'update'])->name('gift-cards.update');
    Route::delete('gift-cards/{gift_card}', [SalonGiftCardController::class, 'destroy'])->name('gift-cards.destroy');
});

Route::prefix('api/salon/data')->name('api.salon.data.')->group(function () {
    Route::get('appointments', [SalonDataController::class, 'appointments']);
    Route::get('customers', [SalonDataController::class, 'customers']);
    Route::get('users', [SalonDataController::class, 'users']);
    Route::get('payments', [SalonDataController::class, 'payments']);
    Route::get('services', [SalonDataController::class, 'services']);
    Route::get('service-categories', [SalonDataController::class, 'serviceCategories']);
    Route::get('booking', [SalonDataController::class, 'bookings']);
    Route::get('technicians/{id}', [SalonDataController::class, 'technician'])->whereNumber('id');
});

Route::name('salon.')->group(function () {
    Route::redirect('/', '/dashboard', 302);

    Route::get('login', [SalonController::class, 'showLogin'])->name('login');
    Route::post('login', [SalonController::class, 'loginPost'])->name('login.post');
    Route::get('logout', [SalonController::class, 'logout'])->name('logout');
    Route::get('forgot-password', [SalonController::class, 'showForgotPassword'])->name('forgot-password');

    Route::middleware('salon.auth')->group(function () {
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
});
