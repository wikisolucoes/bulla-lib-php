<?php

namespace Bulla\lib\GalaxPay;

use \Curl\Curl;
use \Exception;
use \stdClass;

/**
 * [REST] Endpoint /token
 * Objeto responsável por obter o token de acesso que deverá ser utilizado nas requisições à API.
 *
 * @author Felipe Alves <felipe@wikisolucoes.com.br>
 */
class Auth
{

    private $galaxID;
    private $galaxHASH;
    private $galaxHOST;

    private $token;
    private $tokenType;
    private $tokenTime; //em segundos
    private $tokenExpires;

    const SCOPE_LIST = [
        "customers.read", // Clientes: Apenas leitura
        "customers.write", // Clientes: Apenas escrita
        "plans.read", // Planos: Apenas leitura
        "plans.write", // Planos: Apenas escrita
        "cards.read", // Cartões: Apenas leitura
        "cards.write", // Cartões: Apenas escrita
        "subscriptions.read", // Assinaturas: Apenas leitura
        "subscriptions.write", // Assinaturas: Apenas escrita
        "charges.read", // Cobranças Avulsas: Apenas leitura
        "charges.write", // Cobranças Avulsas: Apenas escrita
        "transactions.read", // Transações: Apenas leitura
        "transactions.write", // Transações: Apenas escrita
        "card-brands.read", // Bandeiras de cartões: Apenas leitura
        "carnes.read", // Carnês: Apenas leitura
        "boletos.read", // Boletos: Apenas leitura
        "payment-methods.read", // Formas de pagamento: Apenas leitura
        "webhooks.write", // Webhooks: Apenas escrita
    ];

    public function __construct()
    {
        $this->galaxID = $_ENV['GALAXPAY_API_ID'];
        $this->galaxHASH = $_ENV['GALAXPAY_API_HASH'];
        $this->galaxHOST = $_ENV['GALAXPAY_API_HOST'];
    }

    /**
     * Retornar URL da API
     *
     * @return string $galaxHOST
     */
    public function getApiHost(): string
    {
        return $this->galaxHOST;
    }

    /**
     * Retornar TOKEN da API
     *
     * @return string $token
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Retornar TIPO de TOKEN da API
     *
     * @return string $tokenType
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * [POST] Autenticar na API
     *
     * @param array $scope
     */
    public function login(array $scope = []): void
    {
        if (!$this->tokenIsValid()) {

            $url = $this->galaxHOST . "/token";
            $token = base64_encode($this->galaxID . ':' . $this->galaxHASH);
            $timestamp = time();

            $scope = !count($scope) ? self::SCOPE_LIST : $scope;

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', 'Basic ' . $token);
            $curl->post($url, [
                'grant_type' => 'authorization_code',
                'scope' => implode(' ', $scope),
            ]);

            $response = $curl->response;

            if (isset($response->access_token)) {
                $this->token = $response->access_token;
                $this->tokenType = $response->token_type;
                $this->tokenExpires = $response->expires_in;
                $this->tokenTime = $timestamp;
            } else if (isset($response->error->message)) {
                throw new Exception("Falha na autenticação! \n Error: {$response->error->message}");
            }

            if ($curl->error) {
                throw new Exception("Falha na autenticação! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        }
    }

    /**
     * Verificar tempo de expiração do token
     *
     * @return bool $isValid
     */
    private function tokenIsValid(): bool
    {
        if (!strlen($this->token) || !strlen($this->tokenTime) || !strlen($this->tokenExpires)) {
            return false;
        }

        $expireIn = strtotime("+{$this->tokenExpires} seconds", $this->tokenTime);

        return ($expireIn > time());
    }

    /**
     * Traduzir mensagem de erro da API
     *
     * @param stdClass $error
     * @return string $error
     */
    protected function parseError(stdClass $error): string
    {
        $detailsMsg = "";
        if (isset($error->details)) {
            $details = [];
            foreach (get_object_vars($error->details) as $field => $errors) {
                $msg = "{$field} => " . implode('; ', $errors);
                array_push($details, $msg);
            }
            $detailsMsg = "\n Detalhes: " . implode('\n', $details);
        }

        $message = isset($error->message) ? $error->message : "";

        return "Error: {$message} {$detailsMsg}";
    }
}