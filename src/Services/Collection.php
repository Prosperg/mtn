<?php 

namespace Pkl\Mtn\MomoSdk\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Collection
{

    protected $config, $url, $env;
    
    public function __construct(array $config, $url)
    {
        $this->config = $config['collection'];
        $this->url = $url;
        $this->env = env("MTN_MOMO_ENV");
    }
    
    /**
     * Summary of requestToPay
     * @param string $payerPhoneNumber
     * @param float $amount
     * @param string $currency
     * @param string $payerMessage
     * @param string $payeeNote
     * @throws \Exception
     * @return array{paymentContent: mixed, referenceId: string, status: mixed}
     */
    public function requestToPay(string $payerPhoneNumber, float $amount, 
    string $currency = 'EUR',string $payerMessage = "Aucun msg", string $payeeNote = "Aucune note")
    {
        $referenceId = (string) Str::uuid();
        $externalId = (int) Str::random(10);

        $body = [
            "amount" => $amount, // montant de l'operation
            "currency" => $currency, // la devise de l'operation
            "externalId" => $externalId, // reference
            "payer" => [
                "partyIdType" => "MSISDN",
                "partyId" => $payerPhoneNumber // le numero du client qui paie
            ],
            "payerMessage" => $payerMessage ?? "",
            "payeeNote" => $payeeNote ?? ""
        ];

        $response = Http::withHeaders([
                'X-Reference-Id' => $referenceId,
                'X-Target-Environment' => $this->env,
                'Ocp-Apim-Subscription-Key' => $this->config["primary_key"],
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer " . $this->generateToken()
        ])->post($this->url."/collection/v1_0/requesttopay",$body);

        if(!$response->successful())
        {
            throw new Exception("Erreur lors de paiement par le client : ". $response->body());
        }

        // Temporiseur
        sleep(15);

        // Vérification du status
        $status =  $this->getRequestToPayStatus($referenceId);

        return [
            "referenceId"=>$referenceId,
            "paymentContent"=>$response->body(),
            "status"=>$status->body()
        ];
    }

    /**
     * Summary of getRequestToPayStatus
     * @param mixed $referenceId
     */
    public function getRequestToPayStatus($referenceId)
    {
        try {
            $response = Http::withHeaders([
                'X-Target-Environment' => $this->env,
                'Ocp-Apim-Subscription-Key' => $this->config['primary_key'],
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer " . $this->generateToken()
            ])->get($this->url."/collection/v1_0/requesttopay/$referenceId");

            if(!$response->successful()){
                throw new Exception("Erreur de vérification du status de paiement : ".$response->body());
            }

            return $response;

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
            throw new Exception("Erreur de génération du token : " . $response->body());
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
            throw new Exception("Erreur création API_USER : " . $res->body());
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
            throw new Exception("Erreur création API_KEY : " . $response->body());
        }
        return $response->json('apiKey');
    }
}