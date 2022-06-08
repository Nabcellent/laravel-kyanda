<?php

namespace Nabcellent\Kyanda\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Nabcellent\Kyanda\Models\KyandaTransaction;

class KyandaTransactionFailedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public KyandaTransaction $transaction;

//    TODO: Change this when error returned is confirmed

    /**
     * @param KyandaTransaction $transaction
     */
    public function __construct(KyandaTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
