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
        $kyandaRequests = KyandaRequest::whereDoesntHave('transaction')->get();
        $success = $errors = [];

        foreach ($kyandaRequests as $request) {
            try {
                $status = $this->account->transactionStatus($request->merchant_reference);

//                if (isset($status->message)) {
//                    $errors[$request->merchant_reference] = $status->message;
//                    continue;
//                }

                $success[$request->merchant_reference] = $status['details']->status;

                $data = [
                    'transaction_reference' => $status['details']->transactionRef,
                    'category' => $status['details']->category,
                    'source' => $status['details']->source,
                    'destination' => $status['details']->destination,
                    'merchant_id' => $status['details']->MerchantID,
                    'details' => $status['details']->details,
                    'status' => $status['details']->status,
                    'status_code' => $status['status'],
                    'amount' => $status['details']->amount,
                    'transaction_date' => Carbon::createFromFormat(
                        'd-m-Y g:i a',
                        $status['details']->transactionDate
                    ),
                ];

                $callback = KyandaTransaction::updateOrCreate(
                    ['transaction_reference' => $status['details']->transactionRef],
                    $data
                );

                $this->fireKyandaEvent($callback);
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
    private function fireKyandaEvent(KyandaTransaction $kyandaCallback): void
    {
//        TODO: Check on proper status codes
        if ($kyandaCallback['status_code'] == 0000 || $kyandaCallback['status_code'] == 200) {
            event(new KyandaTransactionSuccessEvent($kyandaCallback));
        } else {
            event(new KyandaTransactionFailedEvent($kyandaCallback));
        }
    }
}
