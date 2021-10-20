<?php

use Illuminate\Support\Facades\Route;
use Nabcellent\Kyanda\Http\Controllers\Controller;

Route::prefix('/kyanda/billing')->namespace(Controller::class)->name('kyanda.')->group(function() {
    Route::get('balance', [Controller::class, 'accountBalance'])->name('account.balance');
    Route::post('transaction-status', [Controller::class, 'transactionStatus'])->name('transaction.status');

    Route::post('airtime/create', [Controller::class, 'airtimePurchase'])->name('airtime.purchase');
    Route::post('bill/create', [Controller::class, 'billPayment'])->name('bill.payment');

//    Route::any('mobile-payout/create', '$routes');
//    Route::any('bank-payout/create', '$routes');
//    Route::any('checkout/create', '$routes');

    Route::post('callback-url/create', [Controller::class, 'registerCallbackURL'])->name('register.callback');
    Route::post('/kyanda/callbacks/notification', [Controller::class, 'instantPaymentNotification']);
});