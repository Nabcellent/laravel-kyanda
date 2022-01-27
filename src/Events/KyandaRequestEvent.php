<?php

namespace Nabcellent\Kyanda\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Nabcellent\Kyanda\Models\KyandaRequest;

class KyandaRequestEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;


    public KyandaRequest $request;

    /**
     * @param KyandaRequest $request
     */
    public function __construct(KyandaRequest $request)
    {
        $this->request = $request;
    }
}
