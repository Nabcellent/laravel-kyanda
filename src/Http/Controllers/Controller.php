<?php

namespace Nabcellent\Kyanda\Http\Controllers;

use Carbon\Carbon;
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
use Nabcellent\Kyanda\Models\KyandaTransaction;
use Nabcellent\Kyanda\Repositories\Kyanda;

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
    public function airtimePurchase(Request $request): array
    {
        $this->validateRequest([
            'phone' => 'required|integer|digits_between:9,12',
            'amount' => 'required|integer'
        ], $request, [
            'phone.required' => 'Phone number is required.',
            'phone.integer' => 'Invalid phone number. Must not start with zero.',
            'phone.digits_between' => 'The phone number must be between 9 and 12 digits long.',
            'amount.integer' => 'Invalid amount. Must not start with zero.',
        ]);

        return Utility::airtimePurchase($request->input('phone'), $request->input('amount'));
    }

    /**
     * @throws KyandaException
     */
    public function billPayment(Request $request): array
    {
        $this->validateRequest([
            'phone' => 'required|integer|digits_between:9,12',
            'account_no' => 'required|integer',
            'amount' => 'required|integer',
            'provider' => 'required|string',
        ], $request, [
            'phone.required' => 'Phone number is required.',
            'phone.integer' => 'Invalid phone number. Must not start with zero.',
            'phone.digits_between' => 'The phone number must be between 9 and 12 digits long.',
            'account_no.integer' => 'Invalid account number. Must not start with zero.',
            'amount.integer' => 'Invalid amount. Must not start with zero.',
            'provider.required' => 'Service provider(telco) is required.',
        ]);

        return Utility::billPayment(
            $request->input('account_no'),
            $request->input('amount'),
            $request->input('provider'),
            $request->input('phone'),
        );
    }



    /**
     * -----------------------------------------------------------------------------------------------    NOTIFICATION
     *
     * @param Request $request
     * @return array
     * @throws KyandaException
     */
    public function registerCallbackURL(Request $request): array
    {
        $url = config('kyanda.urls.callback');

        if (!$url) {
            $this->validateRequest([
                'callback_url' => 'required|url',
            ], $request, [
                'callback_url.url' => 'Invalid callback URL.',
            ]);

            $url = $request->input('callback_url');
        }

        return Notification::registerCallbackURL($url);
    }

    public function instantPaymentNotification(Request $request)
    {
        $data = [
            'reference' => $request->input('transactionRef'),
            'category' => $request->input('category'),
            'source' => $request->input('source'),
            'merchant_id' => $request->input('MerchantID'),
            'details' => $request->input('details'),
            'status' => $request->input('status'),
            'destination' => $request->input('destination'),
            'message' => $request->input('message'),
            'status_code' => $request->input('status_code'),
            'amount' => $request->input('amount'),
            'transaction_date' => Carbon::createFromFormat(
                'd-m-Y g:i a',
                $request->input('transactionDate')
            ),
        ];

        try {
            $transaction = KyandaTransaction::updateOrCreate([
                'reference' => $data['reference']
            ], $data);

            $transaction->request->update([
                'status' => $transaction->status
            ]);

            Kyanda::fireKyandaEvent($transaction);
        } catch (QueryException $e) {
            Log::info('Error updating instant payment notification. - ' . $e->getMessage());
        }
    }



    /**
     * @throws KyandaException
     */
    public function validateRequest(array $rules, Request $request, $messages = [])
    {
        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            throw new KyandaException($validation->errors()->first());
        }
    }
}
