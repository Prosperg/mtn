<?php 

namespace Pkl\Mtn\MomoSdk\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class Disbursement
{
    protected $config, $url,$env;

    /**
     * Summary of __construct
     * @param array $config
     * @param mixed $url
     */
    public function __construct(array $config, $url) {
        $this->config = $config["disbursement"];
        $this->url = $url;
        $this->env = env("MTN_MOMO_ENV");
    }

    /**
     * Summary of deposit
     * @param mixed $amount
     * @param mixed $destinationNumber
     * @param mixed $message
     * @param string $currency
     * @throws \Exception
     * @return array{referenceId: string, status: mixed}
     */
    public function deposit($amount, $destinationNumber, $message = "", string $currency = "EUR")
    {
        
        $referenceId = (string) Str::uuid();
        $body = [
            "amount"=> $amount, // montant
            "currency"=> $currency, //la devise d'opération
            "externalId"=> "124587", //reference
            "payee"=>[
                "partyIdType"=>"MSISDN",
                "partyId"=>$destinationNumber // le numéro de destinateur
            ],
            "payerMessage"=>$message, // Message de l'operation
            "payeeNote"=>"Aucun" // ||
        ];

        $response = Http::withHeaders([
            'X-Reference-Id' => $referenceId,
            'X-Target-Environment' => $this->env,
            'Ocp-Apim-Subscription-Key' => $this->config["primary_key"],
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer " . $this->generateToken()
        ])->post($this->url."/disbursement/v1_0/deposit",$body);

        if (!$response->successful()) {
            throw new \Exception("Erreur de génération du token : " . $response->body());
        }

        //Temporiseur (15 second)
        sleep(15);

        // Vérifier le status du transfer de fond
        $status = $this->getDepositStatus($referenceId);

        return [
            "referenceId"=>$referenceId,
            "status"=>$status
        ];

    }

    /**
     * Summary of checkOwnAccountBalance
     * @throws \Exception
     */
    public function checkOwnAccountBalance()
    {
        $response = Http::withHeaders([
            'X-Target-Environment' => $this->env,
            'Ocp-Apim-Subscription-Key' => $this->config["primary_key"],
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer " . $this->generateToken()
        ])->get($this->url."/disbursement/v1_0/account/balance");

        if (!$response->successful()) {
            throw new \Exception("Erreur lors de la vérication : ". $response->body());
        }

        return $response->body();
    }

    /**
     * Summary of getDepositStatus
     * @param mixed $referenceId
     * @throws \Exception
     */
    public function getDepositStatus($referenceId)
    {
        
        $response = Http::withHeaders([
            'X-Target-Environment' => $this->env,
            'Ocp-Apim-Subscription-Key' => $this->config["primary_key"],
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer " . $this->generateToken()
        ])->get($this->url."/disbursement/v1_0/deposit/$referenceId");
        
        if(!$response->successful())
        {
            throw new \Exception("Erreur de vérification de status de transfert : " . $response->body());
        }
        return $response->body();
    }

    /**
     * Summary of refundBalance
     * Cette methode est utiliser pour rembouser un utilisateur de votre plateforme
     * @param mixed $amount
     * @param mixed $currence
     * @param mixed $payerMessage
     * @param mixed $payeeNote
     * @param mixed $referenceIdToRefund
     * @throws \Exception
     * @return array{referencedId: string, status: mixed}
     */
    public function refundBalance($amount,$currence,$payerMessage,$payeeNote,$referenceIdToRefund)
    {
        $referenceId = (string) Str::uuid();
        $externalId = Str::random(10);

        $body = [
            "amount"=> $amount,
            "currency"=> $currence,
            "externalId"=> $externalId,
            "payerMessage"=> $payerMessage,
            "payeeNote"=> $payeeNote,
            "referenceIdToRefund"=> $referenceIdToRefund
        ];

        $response = Http::withHeaders([
            'X-Reference-Id' => $referenceId,
            'X-Target-Environment' => $this->env,
            'Ocp-Apim-Subscription-Key' => $this->config["primary_key"],
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer " . $this->generateToken()
        ])->post($this->url."/disbursement/v1_0/refund",$body);

        if (!$response->successful()) {
            throw new \Exception("Erreur lors de l'opération de remboussement : ". $response->body());
        }
        
        //Temporiseur
        sleep(15);

        // Vérifier le status du remboussement
        $status = $this->checkRefundStatus($referenceId);

        return [
            "status"=>$status,
            "referencedId"=>$referenceId
        ];
    }

    /**
     * Summary of checkRefundStatus
     * @param mixed $refundUuid
     * @throws \Exception
     */
    public function checkRefundStatus($refundUuid)
    {
        $response = Http::withHeaders([
            'X-Target-Environment' => $this->env,
            'Ocp-Apim-Subscription-Key' => $this->config["primary_key"],
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer " . $this->generateToken()
        ])->get($this->url."disbursement/v1_0/refund/$refundUuid");

        if(!$response->successful())
        {
            throw new \Exception("Erreur lors de la vérification du status : ".$response->body());
        }

        return $response->body();
    }

    /**
     * Summary of generateToken
     * @throws \Exception
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
        ])->post($this->url."/disbursement/token");
    
        if (!$response->successful()) {
            throw new \Exception("Erreur de génération du token : " . $response->body());
        }

        // Récupérer le token
        return $response->json('access_token');
    }

    /**
     * Summary of createApiUser
     * @throws \Exception
     * @return string
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

    /**
     * Summary of createApiKey
     * @param mixed $xReferenceId
     * @throws \Exception
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