<?php

namespace Pkl\Mtn\MomoSdk;
use Pkl\Mtn\MomoSdk\Services\Collection;
use Pkl\Mtn\MomoSdk\Services\Disbursement;
use Pkl\Mtn\MomoSdk\Services\Remittance;

class MomoSdk
{
    protected $url, $config;

    public function __construct(array $config)
    {
        $this->config = $config;

        if(env('MTN_MOMO_ENV') == 'sandbox')
        {
            $this->url = 'https://sandbox.momodeveloper.mtn.com';
        }else{
             $this->url = 'https://proxy.momoapi.mtn.com';
        }
    }

    /**
     * Summary of collection
     * @return Collection
     */
    public function collection(): Collection
    {
        return new Collection($this->config, $this->url);
    }
    
    /**
     * Summary of disbursement
     * @return Disbursement
     */
    public function disbursement(): Disbursement
    {
        return new Disbursement($this->config, $this->url);
    }
    
    /**
     * Summary of remittance
     * @return Remittance
     */
    public function remittance(): Remittance
    {
        return new Remittance($this->config, $this->url);
    }
}