<?php

namespace Bulla\lib\GalaxPay;

use Bulla\lib\Validation;
use \Curl\Curl;
use \Exception;
use \stdClass;

/**
 * [REST] Endpoint /plan
 * Objeto responsável pelo cadastro de planos
 *
 * @author Felipe Alves <felipe@wikisolucoes.com.br>
 */
class Planos extends Auth
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [GET] Retornar listagem de planos
     *
     * @param array $params
     * @param string $order
     * @param int $limit
     * @param int $offset
     *
     * @return array $plans
     */
    public function get(array $params = [], string $order = 'createdAt.asc', int $limit = 100, int $offset = 0): array
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/plans';

            $params['order'] = $order;
            $params['limit'] = $limit;
            $params['startAt'] = $offset;

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->get($url, $params);

            $response = $curl->response;

            if (isset($response->Plans)) {
                return $response->Plans;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao buscar plano! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao buscar planos! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [POST] Criar um novo plano
     *
     * @param array $plan
     *
     * @return stdClass $plan
     */
    public function create(array $plan): stdClass
    {
        try {
            $this->validate($plan);

            $this->login();

            $url = $this->getApiHost() . '/plans';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->post($url, $plan);

            $response = $curl->response;

            if (isset($response->Plan)) {
                return $response->Plan;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao criar plano! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao criar plano! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Editar um plano existente
     *
     * @param array $plan
     *
     * @return stdClass $plan
     */
    public function edit(array $plan): stdClass
    {
        try {
            $this->validate($plan);

            $this->login();

            $url = $this->getApiHost() . '/plans/' . $plan['myId'] . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url, $plan);

            $response = $curl->response;

            if (isset($response->Plan)) {
                return $response->Plan;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao editar plano! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao editar plano! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [DELETE] Excluir um plano
     *
     * @param array $plan
     *
     * @return boolean $type
     */
    public function delete(array $plan): bool
    {
        try {
            if (!isset($plan['myId'])) {
                throw new Exception("Erro ao excluir plano! \n Error: ID não informado!");
            }

            $this->login();

            $url = $this->getApiHost() . '/plans/' . $plan['myId'] . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->delete($url);

            $response = $curl->response;

            if (isset($response->type)) {
                return $response->type;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao excluir plano! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao excluir plano! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Validar os campos do cadastro do plano
     *
     * @param array $plan
     */
    private function validate(array $plan): void
    {
        try {
            $validate = new Validation();

            $validate->set('myId', ($plan['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
            $validate->set('name', ($plan['name'] ?? null))->maxLength(255)->isString()->isRequired(); //Nome do plano.
            $validate->set('periodicity', ($plan['periodicity'] ?? null))->maxLength(255)->isString()->isRequired(); //Periodicidade do plano.
            $validate->set('quantity', ($plan['quantity'] ?? null))->maxLength(3)->isInteger()->isRequired(); //Para indeterminada envie 0.
            $validate->set('additionalInfo', ($plan['additionalInfo'] ?? null))->isString(); //Texto livre dedicado a informações adicionais internas.

            $validate->set('PlanPrices', ($plan['prices'] ?? null))->isArray()->isRequired();
            if (count($plan['prices'] ?? [])) {
                foreach ($plan['prices'] as $k => $price) {
                    $validate->set('PlanPrices.payment', ($price['payment'] ?? null))->maxLength(30)->isString()->isRequired(); //PaymentMethod.id => Id do pagamento
                    $validate->set('PlanPrices.value', ($price['value'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Preço em centavos.
                }
            }

            $validate->validate();
        } catch (Exception $ex) {
            throw new Exception(\nl2br($ex->getMessage()));
        }
    }
}