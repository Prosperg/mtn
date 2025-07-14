<?php 

namespace Pkl\Mtn\MomoSdk\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Collection
{

    protected $config, $url;
    
    public function __construct(array $config, $url)
    {
        $this->config = $config['collection'];
        $this->url = $url;
    }
    
    public function requestToPay(string $referenceId, string $phoneNumber, float $amount, string $currency = 'EUR')
    {
        // Implémentez la logique pour initier un paiement
        $body = [
            "amount" => $amount, // montant de l'operation
            "currency" => $currency, // la devise de l'operation
            "externalId" => $referenceId, // reference
            "payer" => [
                "partyIdType" => "MSISDN",
                "partyId" => $phoneNumber // le numero du client qui paie
            ],
            "payerMessage" => $message ?? "",
            "payeeNote" => $message ?? ""
        ];
        try {
            $response = Http::withHeaders([
                'X-Reference-Id' => $referenceId,
                'X-Target-Environment' => env('APP_MTN_ENV_MODE'),
                'Ocp-Apim-Subscription-Key' => env('MTN_OPC_APIM_SUB_KEY'),
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer " . $this->generateToken()
            ])->post($this->url."/collection/v1_0/requesttopay",["json"=>$body]);

            if($response->getStatusCode() == 202)
            {
                sleep(15);
                return $this->getPaymentStatus($referenceId);
            }
        } catch (\Throwable $th) {
            $response = $th->getMessage();
            return json_decode($response, true);
        }
    }

    /**
     * Summary of getPaymentStatus
     * @param mixed $referenceId
     */
    private function getPaymentStatus($referenceId)
    {
        try {
            $response = Http::withHeaders([
                'X-Target-Environment' => env('APP_MTN_ENV_MODE'),
                'Ocp-Apim-Subscription-Key' => $this->config['primary_key'],
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer " . $this->generateToken()
            ])->get($this->url."/collection/v2_0/payment/$referenceId");
            $re = $response->getBody()->getContents();
            return json_decode($re, true);
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }
    }

     /**
      * Summary of generateToken
      */
    private function generateToken()
    {
        $userApi = $this->createApiUser();
        $apiKey = $this->createApiKey($userApi);
        
        // Encodage de l'identifiant et clé API en Base64
        $credentials = base64_encode("$userApi:$apiKey");

        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->config["primary_key"],
            'Authorization' => "Basic $credentials",
        ])->post($this->url."/collection/token/");
    
        if (!$response->successful()) {
            throw new \Exception("Erreur de génération du token : " . $response->body());
        }

        // Récupérer le token
        return $response->json('access_token');
    }

    /**
     * Summary of createApiUser
     */
    protected function createApiUser()
    {
        $apiUser = (string) Str::uuid();
        $body = ["providerCallbackHost"=> $this->config["callback_url"] ?? "https://tonsite.com/callback"];

        $res = Http::withHeaders([
            "X-Reference-Id"=>$apiUser,
            "Ocp-Apim-Subscription-Key"=>$this->config['primary_key'],
            "Content-Type"=>"application/json"
        ])->post($this->url."/v1_0/apiuser",$body);

        // Optionnel : vérifier que le code est bien 201
        if (!$res->successful()) {
            throw new \Exception("Erreur création API_USER : " . $res->body());
        }
        return $apiUser;
    }

    /***
     * Summary of createApiKey
     */
    protected function createApiKey($xReferenceId)
    {
        $response = Http::withHeaders([
            "Ocp-Apim-Subscription-Key"=>$this->config['primary_key'],
            "Content-Type"=>"application/json"
        ])->post($this->url."/v1_0/apiuser/$xReferenceId/apikey");

        if (!$response->successful()) {
            throw new \Exception("Erreur création API_KEY : " . $response->body());
        }
        return $response->json('apiKey');
    }
}