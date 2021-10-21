<?php

namespace Nabcellent\Kyanda\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Facades\Account;
use Nabcellent\Kyanda\Facades\Notification;
use Nabcellent\Kyanda\Facades\Utility;
use Nabcellent\Kyanda\Models\KyandaRequest;
use Nabcellent\Kyanda\Models\KyandaTransaction;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * -----------------------------------------------------------------------------    ACCOUNT
     */
    /**
     * @return array
     */
    public function accountBalance(): array
    {
        return Account::balance();
    }

    /**
     * @param Request $request
     * @return array
     * @throws KyandaException
     */
    public function transactionStatus(Request $request): array
    {
        if (!$request->has('reference')) {
            throw new KyandaException("Transaction reference is missing.");
        }

        return Account::transactionStatus($request->input('reference'));
    }


    /**
     * -----------------------------------------------------------------------------------------------    UTILITY
     *
     * @throws KyandaException
     */
    public function airtimePurchase(Request $request): KyandaRequest
    {
        $this->validateRequest([
            'phone_number' => 'required|integer',
            'amount' => 'required|numeric'
        ], $request);

        return Utility::airtimePurchase($request->input('phone_number'), $request->input('amount'));
    }

    /**
     * @throws KyandaException
     */
    public function billPayment(Request $request): KyandaRequest
    {
        $this->validateRequest([
            'account_number' => 'required|integer',
            'amount' => 'required|integer',
            'service_provider' => 'required|string',
        ], $request);

        return Utility::billPayment(
            $request->input('accountNumber'),
            $request->input('amount'),
            $request->input('provider')
        );
    }


    /**
     * -----------------------------------------------------------------------------------------------    NOTIFICATION
     *
     * @param Request $request
     * @return array
     */
    public function registerCallbackURL(Request $request): array
    {
        return Notification::registerCallbackURL($request->input('callback_url'));
    }

    public function instantPaymentNotification(Request $request)
    {
        try {
            KyandaTransaction::updateOrCreate($request->only('transactionRef'), $request->all());
        } catch (QueryException $e) {
            Log::info('Error updating instant payment notification.');
        }
    }



    /**
     * @throws KyandaException
     */
    public function validateRequest(array $rules, Request $request)
    {
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            throw new KyandaException($validation->errors()->first());
        }
    }
}
