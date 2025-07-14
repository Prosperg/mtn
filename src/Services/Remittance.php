<?php

namespace Pkl\Mtn\MomoSdk\Services;

class Remittance
{
    protected $config, $url;
    /**
     * Summary of __construct
     * @param array $config
     * @param mixed $url
     */
    public function __construct(array $config, $url) {
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * Summary of generateToken
     */
    private function generateToken()
    {
        
    }
}