<?php

use Illuminate\Support\Facades\Route;
use Nabcellent\Kyanda\Http\Controllers\Controller;

Route::group(['prefix' => 'kyanda/billing/', 'namespace' => Controller::class], function() {
    Route::get('balance', [Controller::class, 'accountBalance']);
    Route::post('transaction-status', [Controller::class, 'transactionStatus']);
    Route::any('mobile-payout/create', '$routes');
    Route::any('bank-payout/create', '$routes');
    Route::any('checkout/create', '$routes');
    Route::post('airtime/create', [Controller::class, 'airtimePurchase']);
    Route::any('bill/create', '$routes');
    Route::post('callback-url/create', [Controller::class, 'registerCallbackURL']);
});