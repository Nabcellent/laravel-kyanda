<?php

namespace Nabcellent\Kyanda\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Nabcellent\Kyanda\Models\KyandaRequest;

class KyandaRequestEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param KyandaRequest $request
     */
    public function __construct(
        public KyandaRequest $request
    )
    {
    }
}