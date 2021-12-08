<?php

namespace Bulla\lib\GalaxPay;

use Bulla\lib\Validation;
use \Curl\Curl;
use \Exception;
use \stdClass;

/**
 * [REST] Endpoint /webhook
 * Objeto responsável pelo cadastro de webhooks
 *
 * @author Felipe Alves <felipe@wikisolucoes.com.br>
 */
class Webhooks extends Auth
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [PUT] Editar um webhook existente
     *
     * @param array $webhook
     *
     * @return stdClass $webhook
     */
    public function edit(array $webhook): stdClass
    {
        try {
            $this->validate($webhook);

            $this->login();

            $url = $this->getApiHost() . '/webhooks';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url, $webhook);

            $response = $curl->response;

            if (isset($response->confirmHash)) {
                return $response;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao editar webhook! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao editar webhook! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Validar os campos do cadastro do webhook
     *
     * @param array $webhook
     */
    private function validate(array $webhook): void
    {
        try {
            $validate = new Validation();

            $validate->set('url', ($webhook['url'] ?? null))->maxLength(255)->isString(); //URL do Webhook.

            if (count($webhook['events'] ?? null)) {
                foreach ($webhook['events'] as $k => $event) {
                    $validate->set('events', $event)->maxLength(255)->isString(); //Array de eventos a serem ativados.
                }
            }

            $validate->validate();
        } catch (Exception $ex) {
            throw new Exception(\nl2br($ex->getMessage()));
        }
    }
}