<?php

namespace Nabcellent\Kyanda\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Nabcellent\Kyanda\Models\KyandaTransaction;

class KyandaTransactionSuccessEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public KyandaTransaction $transaction;

    /**
     * @param KyandaTransaction $transaction
     */
    public function __construct(KyandaTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
