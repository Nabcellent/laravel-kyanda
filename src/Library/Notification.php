<?php

namespace Nabcellent\Kyanda\Library;

use Nabcellent\Kyanda\Exceptions\KyandaException;

/**
 * Class Notification
 * @package Nabcellent\Kyanda\Library
 */
class Notification extends Core
{
//    TODO: callback registration
//    Add callback registration function/process here
    /**
     * @param string|null $url
     * @return array
     * @throws KyandaException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function registerCallbackURL(string $url = null): array
    {
        $url = $url ?? config('kyanda.urls.callback');

        if (!$url) {
            throw new KyandaException("No callback url provided.");
        }

        $body["callbackURL"] = $url;

        return $this->request('callback_register', $body);
    }
}
