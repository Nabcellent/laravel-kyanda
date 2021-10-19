<?php

namespace Nabcellent\Kyanda\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Nabcellent\Kyanda\Exceptions\KyandaException;
use Nabcellent\Kyanda\Library\Account;
use Nabcellent\Kyanda\Library\Notification;
use Nabcellent\Kyanda\Library\Utility;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private Account $account;
    private Notification $notification;
    private Utility $utility;

    public function __construct()
    {

    }

    /**
     * ---------------------------------------------------------------------------------------------------------    ACCOUNT
    */
    /**
     * @throws KyandaException
     */
    public function accountBalance(): array
    {
        return $this->account->balance();
    }

    /**
     * @throws KyandaException
     */
    public function transactionStatus(Request $request): array
    {
        if(!$request->has('reference')) throw new KyandaException("Transaction reference is missing.");

        return $this->account->transactionStatus($request->input('reference'));
    }


    /**
     * ---------------------------------------------------------------------------------------------------------    UTILITY
     *
     * @throws KyandaException
     */
    public function airtimePurchase(Request $request): array {
        $validation = Validator::make($request->all(), [
            'phone_number' => 'required|int',
            'amount' => 'required|number'
        ]);

        if($validation->fails()) throw new KyandaException($validation->errors()->first());

        return $this->utility->airtimePurchase($request->input('phone_number'), $request->input('amount'));
    }


    /**
     * ---------------------------------------------------------------------------------------------------------    IPN
     *
     * @throws KyandaException
     */
    public function registerCallbackURL(Request $request): array {
        return $this->notification->registerCallbackURL($request->input('callback_url'));
    }
}
