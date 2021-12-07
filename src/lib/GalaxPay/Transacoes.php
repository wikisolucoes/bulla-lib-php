<?php

namespace Bulla\lib\GalaxPay;

use Bulla\lib\Validation;
use \Curl\Curl;
use \Exception;
use \stdClass;

/**
 * [REST] Endpoint /transaction
 * Objeto responsável por consultar e editar transações
 *
 * @author Felipe Alves <felipe@wikisolucoes.com.br>
 */
class Transacoes extends Auth
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [GET] Retornar listagem de transações
     *
     * @param array $params
     * @param string $order
     * @param int $limit
     * @param int $offset
     *
     * @return array $transactions
     */
    public function get(array $params = [], string $order = 'createdAt.asc', int $limit = 100, int $offset = 0): array
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/transactions';

            $params['order'] = $order;
            $params['limit'] = $limit;
            $params['startAt'] = $offset;

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->get($url, $params);

            $response = $curl->response;

            if (isset($response->Transactions)) {
                return $response->Transactions;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao buscar transação! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao buscar transações! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Editar uma transação existente
     *
     * @param array $transaction
     *
     * @return stdClass $transaction
     */
    public function edit(array $transaction): stdClass
    {
        try {
            $this->validate($transaction);

            $this->login();

            $url = $this->getApiHost() . '/transactions/' . $transaction['myId'] . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url, $transaction);

            $response = $curl->response;

            if (isset($response->Transaction)) {
                return $response->Transaction;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao editar transação! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao editar transação! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Validar os campos do cadastro do transação
     *
     * @param array $transaction
     */
    private function validate(array $transaction): void
    {
        try {
            $validate = new Validation();

            $validate->set('myId', $transaction['myId'])->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
            $validate->set('value', $transaction['value'])->maxLength(11)->isInteger(); //Preço em centavos.
            $validate->set('payday', $transaction['payday'])->maxLength(10)->isString(); //Data de vencimento do pagamento.
            $validate->set('payedOutsideGalaxPay', $transaction['payedOutsideGalaxPay'])->isBoolean(); //Define se a cobrança foi paga fora do sistema do Galax Pay.
            $validate->set('additionalInfo', $transaction['additionalInfo'])->isString(); //Texto para informações adicionais sobre a transação.

            $validate->validate();
        } catch (Exception $ex) {
            throw new Exception(\nl2br($ex->getMessage()));
        }
    }
}