<?php

namespace Bulla\lib\GalaxPay;

use \Curl\Curl;
use \Exception;

/**
 * [REST] Endpoint /carne
 * Objeto responsável pela consulta de carnês para impressão
 *
 * @author Felipe Alves <felipe@wikisolucoes.com.br>
 */
class Boletos extends Auth
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [POST] Retorna informações sobre até 20 carnês por assinatura ou 100 transações/cobranças avulsas.
     *
     * @param string $typePDF [onePDFSubscription, onePDFCharge, onePDFTransaction, onePDFBySubscription]
     * @param string $typeCover [none, customer, subscription-or-charge]
     * @param array $myIDs
     *
     * @return array $carnes
     */
    public function get(string $typePDF, string $typeCover, array $myIDs): stdClass
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/carnes/' . $typePDF . '/' . $typeCover;

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->post($url, [
                'myIds' => $myIDs,
            ]);

            $response = $curl->response;

            if (isset($response->Carnes)) {
                return $response->Carnes;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao buscar boletos! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao buscar boletos! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [POST] Retorna um arquivo PDF com as informações sobre até 100 boletos solicitados.
     *
     * @param string $entityType Tipo dos ids. Opções: [transactions, charges]
     * @param array $myIDs
     *
     * @return array $carnes
     */
    public function getPDF(string $entityType, array $myIDs): stdClass
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/boletos/' . $entityType;

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->post($url, [
                'myIds' => $myIDs,
            ]);

            $response = $curl->response;

            if (isset($response->Boleto)) {
                return $response->Boleto;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao buscar boletos! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao buscar boletos! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}