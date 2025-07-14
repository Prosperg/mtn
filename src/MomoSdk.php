<?php

namespace Pkl\Mtn\MomoSdk;
use Illuminate\Support\Facades\Http;
use Pkl\Mtn\MomoSdk\Services\Collection;
use Pkl\Mtn\MomoSdk\Services\Disbursement;
use Pkl\Mtn\MomoSdk\Services\Remittance;

class MomoSdk
{
    protected $url,$depositToken, $config;

    public function __construct(array $config)
    {
        if(env('MTN_MOMO_ENV') == 'sandbox')
        {
            $this->url = 'https://sandbox.momodeveloper.mtn.com';
        }else{
             $this->url = 'https://proxy.momoapi.mtn.com';
        }
        // $this->depositToken = $this->generateDepositToken();
        $this->config = $config;
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


    //  /**
    //  * Summary of generateDepositToken
    //  */
    // private function generateDepositToken()
    // {
    //     $response = Http::withHeaders([
    //         'Ocp-Apim-Subscription-Key' => env('DEPOSIT_MTN_OPC_APIM_SUB_KEY'),
    //         'Authorization' => env('DEPOSIT_AUTHORIZATION'),
    //     ])->post($this->url."/disbursement/token/");
    
    //     // Récupérer le contenu de la réponse
    //     $body = $response->body();
    //     //Retourner le token
    //     return (json_decode($body))->access_token;
    // }

    // /**
    //  * Summary of generateMtnToken
    //  */
    // private function generateToken()
    // {
    //     $response = Http::withHeaders([
    //         'Ocp-Apim-Subscription-Key' => env('MTN_OPC_APIM_SUB_KEY'),
    //         'Authorization' => env('AUTHORIZATION'),
    //     ])->post($this->url."/collection/token/");
    
    //     // Récupérer le contenu de la réponse
    //     $body = $response->body();
    //     $body_j = json_decode($body);
    
    //     return $body_j;
    // }

    // /**
    //  * Summary of deposit
    //  * @param mixed $referebceId
    //  * @param mixed $amount
    //  * @param mixed $destinationNumber
    //  * @param mixed $message
    //  */
    // public function deposit($referebceId, $amount, $destinationNumber, $message)
    // {
    //     $body = [
    //         "amount"=> $amount, // montant
    //         "currency"=>"XOF", //la devise d'opération
    //         "externalId"=> "124587", //reference
    //         "payee"=>[
    //             "partyIdType"=>"MSISDN",
    //             "partyId"=>$destinationNumber // le numéro de destinateur
    //         ],
    //         "payerMessage"=>$message, // Message de l'operation
    //         "payeeNote"=>"Aucun" // ||
    //     ];

    //     try {

    //         $response = Http::withHeaders([
    //             'X-Reference-Id' => $referebceId,
    //             'X-Target-Environment' => env('APP_MTN_ENV_MODE'),
    //             'Ocp-Apim-Subscription-Key' => env('DEPOSIT_MTN_OPC_APIM_SUB_KEY'),
    //             'Content-Type' => 'application/json',
    //             'Authorization' => "Bearer " . $this->generateToken()
    //         ])->post($this->url."/disbursement/v1_0/deposit",["json"=>$body]);

    //         if($response->getStatusCode()==202 || $response->getStatusCode()==200){
    //             // sleep(15);

    //             $status = $this->getDepositStatus($referebceId);

    //             return json_decode($status->getBody()->getContents() , true);
    //         }

    //     } catch (\Throwable $th) {
    //         $response = $th->getMessage();
    //         return json_decode($response, true);
    //     }
    // }

    // /**
    //  * Summary of requestToPay
    //  * @param mixed $partyId
    //  * @param mixed $referenceId
    //  * @param mixed $amount
    //  * @param mixed $message
    //  */
    // public function requestToPay($partyId, $referenceId, $amount, $message)
    // {
    //     // Implémentez la logique pour initier un paiement
    //     $body = [
    //         "amount" => $amount, // montant de l'operation
    //         "currency" => "XOF", // la devise de l'operation
    //         "externalId" => $referenceId, // reference
    //         "payer" => [
    //             "partyIdType" => "MSISDN",
    //             "partyId" => $partyId // le numero du client qui paie
    //         ],
    //         "payerMessage" => $message,
    //         "payeeNote" => $message
    //     ];
    //     try {
    //         $response = Http::withHeaders([
    //             'X-Reference-Id' => $referenceId,
    //             'X-Target-Environment' => env('APP_MTN_ENV_MODE'),
    //             'Ocp-Apim-Subscription-Key' => env('MTN_OPC_APIM_SUB_KEY'),
    //             'Content-Type' => 'application/json',
    //             'Authorization' => "Bearer " . $this->generateToken()
    //         ])->post($this->url."/collection/v1_0/requesttopay",["json"=>$body]);

    //         if($response->getStatusCode() == 202)
    //         {
    //             sleep(15);
    //             return $this->getPaymentStatus($referenceId);
    //         }
    //     } catch (\Throwable $th) {
    //         $response = $th->getMessage();
    //         return json_decode($response, true);
    //     }
        
    // }

    // /**
    //  * Summary of getDepositStatus
    //  * @param mixed $referenceId
    //  */
    // private function getDepositStatus($referenceId)
    // {
        
    //     $response = Http::withHeaders([
    //         'X-Target-Environment' => env('APP_MTN_ENV_MODE'),
    //         'Ocp-Apim-Subscription-Key' => env('DEPOSIT_MTN_OPC_APIM_SUB_KEY'),
    //         'Content-Type' => 'application/json',
    //         'Authorization' => "Bearer " . $this->depositToken
    //     ])->get($this->url."/disbursement/v1_0/deposit/$referenceId");
        

    //     $re = $response->getBody()->getContents();
    //     return json_decode($re, true);
    // }

    // /**
    //  * Summary of getPaymentStatus
    //  * @param mixed $referenceId
    //  */
    // private function getPaymentStatus($referenceId)
    // {
    //     try {
    //         $response = Http::withHeaders([
    //             'X-Target-Environment' => env('APP_MTN_ENV_MODE'),
    //             'Ocp-Apim-Subscription-Key' => env('MTN_OPC_APIM_SUB_KEY'),
    //             'Content-Type' => 'application/json',
    //             'Authorization' => "Bearer " . $this->depositToken
    //         ])->get($this->url."/collection/v2_0/payment/$referenceId");
    //         $re = $response->getBody()->getContents();
    //         return json_decode($re, true);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         return $th->getMessage();
    //     }
    // }
}