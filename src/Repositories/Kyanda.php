<?php

namespace Nabcellent\Kyanda\Repositories;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Nabcellent\Kyanda\Events\KyandaTransactionFailedEvent;
use Nabcellent\Kyanda\Events\KyandaTransactionSuccessEvent;
use Nabcellent\Kyanda\Library\Account;
use Nabcellent\Kyanda\Library\BaseClient;
use Nabcellent\Kyanda\Models\KyandaRequest;
use Nabcellent\Kyanda\Models\KyandaTransaction;

class Kyanda
{
    private Account $account;

    public function __construct(BaseClient $baseClient)
    {
        $this->account = new Account($baseClient);
    }

    /**
     * @return array[]
     */
    public function queryTransactionStatus(): array
    {
        /** @var KyandaRequest[] $kyandaRequests */
        $kyandaRequests = KyandaRequest::where('status', '<>', 'Failed')->whereDoesntHave('transaction')->get();
        $success = $errors = [];

        foreach ($kyandaRequests as $request) {
            try {
                $status = $this->account->transactionStatus($request->merchant_reference);

                $success[$request->merchant_reference] = $status['details']->Status;

                $data = [
                    'transaction_reference' => $status['details']->transactionRef,
                    'category' => $status['details']->Category,
                    'source' => $status['details']->source,
                    'merchant_id' => $status['details']->MerchantID,
                    'details' => $status['details']->details,
                    'destination' => $status['details']->Phone,
                    'status' => $status['details']->Status,
                    'message' => $status['details']->message,
                    'status_code' => $status['status'],
                    'amount' => $status['details']->Amount,
                    'transaction_date' => Carbon::createFromFormat(
                        'd-m-Y g:i a',
                        $status['details']->Posted_Time
                    ),
                ];

                $transaction = KyandaTransaction::updateOrCreate(
                    ['transaction_reference' => $status['details']->transactionRef],
                    $data
                );

                $request->update([
                    'status' => $transaction->status
                ]);

                self::fireKyandaEvent($transaction);
            } catch (Exception | GuzzleException $e) {
                $errors[$request->merchant_reference] = $e->getMessage();
            }
        }
        return ['successful' => $success, 'errors' => $errors];
    }

    /**
     * @param KyandaTransaction $kyandaCallback
     * @return void
     */
    public static function fireKyandaEvent(KyandaTransaction $kyandaCallback): void
    {
//        TODO: Check on proper status codes
        if (in_array($kyandaCallback['status_code'], [0000])) {
            event(new KyandaTransactionSuccessEvent($kyandaCallback));
        } else {
            event(new KyandaTransactionFailedEvent($kyandaCallback));
        }
    }
}
