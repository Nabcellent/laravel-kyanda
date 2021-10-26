<?php

namespace Nabcellent\Kyanda\Repositories;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Nabcellent\Kyanda\Events\KyandaTransactionFailedEvent;
use Nabcellent\Kyanda\Events\KyandaTransactionSuccessEvent;
use Nabcellent\Kyanda\Facades\Account;
use Nabcellent\Kyanda\Models\KyandaRequest;
use Nabcellent\Kyanda\Models\KyandaTransaction;

class Kyanda
{
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
                $status = Account::transactionStatus($request->merchant_reference);

                if (isset($status->errorMessage)) {
                    $errors[$request->merchant_reference] = $status->errorMessage;
                    continue;
                }

                $success[$request->merchant_reference] = $status['message'];

                $callback = KyandaTransaction::updateOrCreate($status['transactionRef'], $status);

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
        if ($kyandaCallback['status_code'] == 0000) {
            event(new KyandaTransactionSuccessEvent($kyandaCallback));
        } else {
            event(new KyandaTransactionFailedEvent($kyandaCallback));
        }
    }
}
