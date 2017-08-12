<?php

namespace DeveoDK\CoreTimeTracker\Services;

use Illuminate\Http\Request;

class RequestService
{
    /** @var Request */
    protected $request;

    /**
     * RequestService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the correct Ip from a request
     * @return string
     */
    public function getRequestIp()
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip,
                            FILTER_VALIDATE_IP,
                            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
                        ) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return "127.0.0.1";
    }

    /**
     * @return array|string
     */
    public function getUserAgent()
    {
        return $this->request->header('User-Agent');
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->request->header('Origin');
    }
}
