<?php

namespace Bulla\lib\GalaxPay;

use Bulla\lib\Validation;
use \Curl\Curl;
use \Exception;
use \stdClass;

/**
 * [REST] Endpoint /customer
 * Objeto responsável pelo cadastro de clientes
 *
 * @author Felipe Alves <felipe@wikisolucoes.com.br>
 */
class Clientes extends Auth
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [GET] Retornar listagem de clientes
     *
     * @param array $params
     * @param string $order
     * @param int $limit
     * @param int $offset
     *
     * @return array $customers
     */
    public function get(array $params = [], string $order = 'createdAt.asc', int $limit = 100, int $offset = 0): array
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/customers';

            $params['order'] = $order;
            $params['limit'] = $limit;
            $params['startAt'] = $offset;

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->get($url, $params);

            $response = $curl->response;

            if (isset($response->Customers)) {
                return $response->Customers;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao buscar cliente! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao buscar clientes! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [POST] Criar um novo cliente
     *
     * @param array $customer
     *
     * @return stdClass $customer
     */
    public function create(array $customer): stdClass
    {
        try {
            $this->validate($customer);

            $this->login();

            $url = $this->getApiHost() . '/customers';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->post($url, $customer);

            $response = $curl->response;

            if (isset($response->Customer)) {
                return $response->Customer;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao criar cliente! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao criar cliente! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Editar um cliente existente
     *
     * @param array $customer
     *
     * @return stdClass $customer
     */
    public function edit(array $customer): stdClass
    {
        try {
            $this->validate($customer);

            $this->login();

            $url = $this->getApiHost() . '/customers/' . $customer['myId'] . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url, $customer);

            $response = $curl->response;

            if (isset($response->Customer)) {
                return $response->Customer;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao editar cliente! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao editar cliente! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [DELETE] Excluir um cliente
     *
     * @param array $customer
     *
     * @return boolean $type
     */
    public function delete(array $customer): bool
    {
        try {
            if (!isset($customer['myId'])) {
                throw new Exception("Erro ao excluir cliente! \n Error: ID não informado!");
            }

            $this->login();

            $url = $this->getApiHost() . '/customers/' . $customer['myId'] . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->delete($url);

            $response = $curl->response;

            if (isset($response->type)) {
                return $response->type;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao excluir cliente! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao excluir cliente! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Validar os campos do cadastro do cliente
     *
     * @param array $customer
     */
    private function validate(array $customer): void
    {
        try {
            $validate = new Validation();

            $validate->set('myId', ($customer['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
            $validate->set('name', ($customer['name'] ?? null))->maxLength(255)->isString()->isRequired(); //Nome ou razão social do cliente.
            $validate->set('document', ($customer['document'] ?? null))->maxLength(14)->isString()->isRequired(); //CPF OU CNPJ do cliente. Apenas números.
            $validate->set('invoiceHoldIss', ($customer['invoiceHoldIss'] ?? null))->isBoolean(); //Se reterá ISS na nota fiscal ou não.
            $validate->set('municipalDocument', ($customer['municipalDocument'] ?? null))->maxLength(255)->isString(); //Inscrição municipal do cliente.

            $validate->set('Address.zipCode', ($customer['Address']['zipCode'] ?? null))->maxLength(8)->isString()->isRequired(); //CEP. Informe apenas números.
            $validate->set('Address.street', ($customer['Address']['street'] ?? null))->maxLength(255)->isString()->isRequired(); //Logradouro.
            $validate->set('Address.number', ($customer['Address']['number'] ?? null))->maxLength(255)->isString()->isRequired(); //Número.
            $validate->set('Address.complement', ($customer['Address']['complement'] ?? null))->maxLength(255)->isString(); //Complemento.
            $validate->set('Address.neighborhood', ($customer['Address']['neighborhood'] ?? null))->maxLength(255)->isString()->isRequired(); //Bairro.
            $validate->set('Address.city', ($customer['Address']['city'] ?? null))->maxLength(255)->isString()->isRequired(); //Cidade.
            $validate->set('Address.state', ($customer['Address']['state'] ?? null))->maxLength(2)->isString()->isRequired(); //Estado.

            $validate->set('Emails', ($customer['emails'] ?? null))->isArray()->isRequired();
            if (count($customer['emails'] ?? [])) {
                foreach ($customer['emails'] as $k => $email) {
                    $validate->set('emails', $email)->maxLength(255)->isString()->isRequired(); //Emails do cliente.
                }
            }

            if (count($customer['phones'] ?? [])) {
                foreach ($customer['phones'] as $k => $phone) {
                    $validate->set('phones', $phone)->maxLength(11)->isInteger(); //Telefones do cliente.
                }
            }

            $validate->validate();
        } catch (Exception $ex) {
            throw new Exception(\nl2br($ex->getMessage()));
        }
    }
}