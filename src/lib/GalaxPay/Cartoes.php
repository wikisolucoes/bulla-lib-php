<?php

namespace Bulla\lib\GalaxPay;

use Bulla\lib\Validation;
use \Curl\Curl;
use \Exception;
use \stdClass;

/**
 * [REST] Endpoint /card
 * Objeto responsável pelo cadastro de cartões
 *
 * @author Felipe Alves <felipe@wikisolucoes.com.br>
 */
class Cartoes extends Auth
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [GET] Retornar listagem de cartões
     *
     * @param array $params
     * @param string $order
     * @param int $limit
     * @param int $offset
     *
     * @return array $cards
     */
    public function get(array $params = [], string $order = 'createdAt.asc', int $limit = 100, int $offset = 0): array
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/cards';

            $params['order'] = $order;
            $params['limit'] = $limit;
            $params['startAt'] = $offset;

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->get($url, $params);

            $response = $curl->response;

            if (isset($response->Cards)) {
                return $response->Cards;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao buscar cartão! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao buscar cartões! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [POST] Criar um novo cartão
     *
     * @param array $card
     * @param int $customerId
     *
     * @return stdClass $card
     */
    public function create(array $card, int $customerId): stdClass
    {
        try {
            $this->validate($card);

            $this->login();

            $url = $this->getApiHost() . '/cards/' . $customerId . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->post($url, $card);

            $response = $curl->response;

            if (isset($response->Card)) {
                return $response->Card;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao criar cartão! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao criar cartão! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Validar os campos do cadastro do cartão
     *
     * @param array $card
     */
    private function validate(array $card): void
    {
        try {
            $validate = new Validation();

            $validate->set('myId', ($card['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
            $validate->set('number', ($card['number'] ?? null))->maxLength(30)->isString()->isRequired(); //Número do cartão.
            $validate->set('holder', ($card['holder'] ?? null))->maxLength(30)->isString()->isRequired(); //Nome do portador.
            $validate->set('expiresAt', ($card['expiresAt'] ?? null))->maxLength(7)->isString()->isRequired(); //String contendo ano e mês de expiração do cartão. YYYY-MM
            $validate->set('cvv', ($card['cvv'] ?? null))->maxLength(4)->isString()->isRequired(); //Código de segurança.

            $validate->validate();
        } catch (Exception $ex) {
            throw new Exception(\nl2br($ex->getMessage()));
        }
    }
}