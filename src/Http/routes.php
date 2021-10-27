<?php

use Illuminate\Support\Facades\Route;
use Nabcellent\Kyanda\Http\Controllers\Controller;

Route::prefix('/kyanda')->namespace(Controller::class)->name('kyanda.')->group(function () {
    Route::prefix('/billing')->group(function () {
        Route::get('balance', [Controller::class, 'accountBalance'])->name('account.balance');
        Route::post('transaction-status', [Controller::class, 'transactionStatus'])->name('transaction.status');

        Route::post('airtime/create', [Controller::class, 'airtimePurchase'])->name('airtime.purchase');
        Route::post('bill/create', [Controller::class, 'billPayment'])->name('bill.payment');s

//    Route::any('mobile-payout/create', '$routes');
//    Route::any('bank-payout/create', '$routes');
//    Route::any('checkout/create', '$routes');
    });

    Route::prefix('/callbacks')->name('callback.')->group(function () {
        Route::post('/register', [Controller::class, 'registerCallbackURL'])->name('register');
        Route::post('/notification', [Controller::class, 'instantPaymentNotification'])->name('notification');
    });
});
