<?php

namespace Bulla\lib\GalaxPay;

use Bulla\lib\Validation;
use \Curl\Curl;
use \Exception;
use \stdClass;

/**
 * [REST] Endpoint /charge
 * Objeto responsável pelo cadastro de cobranças
 *
 * @author Felipe Alves <felipe@wikisolucoes.com.br>
 */
class CobrancaAvulsa extends Auth
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [GET] Retornar listagem de cobranças
     *
     * @param array $params
     * @param string $order
     * @param int $limit
     * @param int $offset
     *
     * @return array $charges
     */
    public function get(array $params = [], string $order = 'createdAt.asc', int $limit = 100, int $offset = 0): array
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/charges';

            $params['order'] = $order;
            $params['limit'] = $limit;
            $params['startAt'] = $offset;

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->get($url, $params);

            $response = $curl->response;

            if (isset($response->Charges)) {
                return $response->Charges;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao buscar cobrança! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao buscar cobranças! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [POST] Criar um novo cobrança
     *
     * @param array $charge
     *
     * @return stdClass $charge
     */
    public function create(array $charge): stdClass
    {
        try {
            $this->validate($charge);

            $this->login();

            $url = $this->getApiHost() . '/charges';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->post($url, $charge);

            $response = $curl->response;

            if (isset($response->Charge)) {
                return $response->Charge;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao criar cobrança! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao criar cobrança! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Editar um cobrança existente
     *
     * @param array $charge
     *
     * @return stdClass $charge
     */
    public function edit(array $charge): stdClass
    {
        try {
            $this->validate($charge);

            $this->login();

            $url = $this->getApiHost() . '/charges/' . $charge['myId'] . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url, $charge);

            $response = $curl->response;

            if (isset($response->Charge)) {
                return $response->Charge;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao editar cobrança! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao editar cobrança! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Método utilizado para tentar novamente realizar a captura de uma cobrança avulsa.
     *
     * @param int $chargeId
     *
     * @return stdClass $charge
     */
    public function retry(int $chargeId): stdClass
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/charges/' . $chargeId . '/myId/retry';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url);

            $response = $curl->response;

            if (isset($response->Transaction)) {
                return $response->Transaction;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao tentar captar cobrança avulsa! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao tentar captar cobrança avulsa! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Método utilizado para realizar o estorno da transação de uma cobrança avulsa.
     *
     * @param int $chargeId
     *
     * @return stdClass $charge
     */
    public function reverse(int $chargeId): stdClass
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/charges/' . $chargeId . '/myId/reverse';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url);

            $response = $curl->response;

            if (isset($response->Charge)) {
                return $response->Charge;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao tentar estornar cobrança! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao tentar estornar cobrança! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Método utilizado para realizar a captura de uma cobrança avulsa.
     *
     * @param int $chargeId
     *
     * @return stdClass $charge
     */
    public function capture(int $chargeId): stdClass
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/charges/' . $chargeId . '/myId/capture';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url);

            $response = $curl->response;

            if (isset($response->Charge)) {
                return $response->Charge;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao tentar estornar cobrança! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao tentar estornar cobrança! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [DELETE] Excluir um cobrança
     *
     * @param array $charge
     *
     * @return boolean $type
     */
    public function delete(array $charge): bool
    {
        try {
            if (!isset($charge['myId'])) {
                throw new Exception("Erro ao excluir cobrança! \n Error: ID não informado!");
            }

            $this->login();

            $url = $this->getApiHost() . '/charges/' . $charge['myId'] . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->delete($url);

            $response = $curl->response;

            if (isset($response->type)) {
                return $response->type;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao excluir cobrança! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao excluir cobrança! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Validar os campos do cadastro do cobrança
     *
     * @param array $charge
     */
    private function validate(array $charge): void
    {
        try {
            $validate = new Validation();

            $validate->set('myId', ($charge['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
            $validate->set('value', ($charge['value'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Preço em centavos.
            $validate->set('additionalInfo', ($charge['additionalInfo'] ?? null))->isString(); //Texto livre dedicado a informações adicionais internas.
            $validate->set('payday', ($charge['payday'] ?? null))->maxLength(10)->isString(); //Data de vencimento do pagamento.
            $validate->set('payedOutsideGalaxPay', ($charge['payedOutsideGalaxPay'] ?? null))->isBoolean(); //Define se a cobrança foi paga fora do sistema do Galax Pay.
            $validate->set('mainPaymentMethodId', ($charge['mainPaymentMethodId'] ?? null))->maxLength(30)->isString(); //PaymentMethod.id => Id do pagamento principal.

            $validate->set('Customer.myId', ($charge['Customer']['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
            $validate->set('Customer.name', ($charge['Customer']['name'] ?? null))->maxLength(255)->isString()->isRequired(); //Nome ou razão social do cliente.
            $validate->set('Customer.document', ($charge['Customer']['document'] ?? null))->maxLength(255)->isString()->isRequired(); //CPF OU CNPJ do cliente. Apenas números.
            $validate->set('Customer.invoiceHoldIss', ($charge['Customer']['invoiceHoldIss'] ?? null))->maxLength(255)->isString(); //Se reterá ISS na nota fiscal ou não.
            $validate->set('Customer.municipalDocument', ($charge['Customer']['municipalDocument'] ?? null))->maxLength(255)->isString(); //Inscrição municipal do cliente.

            $validate->set('Customer.emails', ($charge['Customer']['emails'] ?? null))->isArray()->isRequired();
            if (count($charge['Customer']['emails'] ?? [])) {
                foreach ($charge['Customer']['emails'] as $k => $email) {
                    $validate->set('Customer.email', $email)->maxLength(255)->isString()->isRequired(); //Emails do cliente.
                }
            }

            if (count($charge['Customer']['phones'] ?? [])) {
                foreach ($charge['Customer']['phones'] as $k => $phone) {
                    $validate->set('Customer.phone', $phone)->maxLength(11)->isInteger(); //Telefones do cliente.
                }
            }

            if (count(($charge['Customer']['Address'] ?? []))) {
                $validate->set('Customer.Address.zipCode', ($charge['Customer']['Address']['zipCode'] ?? null))->maxLength(8)->isString()->isRequired(); //CEP. Informe apenas números.
                $validate->set('Customer.Address.street', ($charge['Customer']['Address']['street'] ?? null))->maxLength(255)->isString()->isRequired(); //Logradouro.
                $validate->set('Customer.Address.number', ($charge['Customer']['Address']['number'] ?? null))->maxLength(255)->isString()->isRequired(); //Número.
                $validate->set('Customer.Address.complement', ($charge['Customer']['Address']['complement'] ?? null))->maxLength(255)->isString(); //Complemento.
                $validate->set('Customer.Address.neighborhood', ($charge['Customer']['Address']['neighborhood'] ?? null))->maxLength(255)->isString()->isRequired(); //Bairro.
                $validate->set('Customer.Address.city', ($charge['Customer']['Address']['city'] ?? null))->maxLength(255)->isString()->isRequired(); //Cidade.
                $validate->set('Customer.Address.state', ($charge['Customer']['Address']['state'] ?? null))->maxLength(2)->isString()->isRequired(); //Estado.
            }

            if (count($charge['PaymentMethodCreditCard'] ?? [])) {
                $validate->set('PaymentMethodCreditCard.Card.myId', ($charge['PaymentMethodCreditCard']['Card']['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
                $validate->set('PaymentMethodCreditCard.Card.number', ($charge['PaymentMethodCreditCard']['Card']['number'] ?? null))->maxLength(30)->isString()->isRequired(); //Número do cartão.
                $validate->set('PaymentMethodCreditCard.Card.holder', ($charge['PaymentMethodCreditCard']['Card']['holder'] ?? null))->maxLength(30)->isString()->isRequired(); //Nome do portador.
                $validate->set('PaymentMethodCreditCard.Card.expiresAt', ($charge['PaymentMethodCreditCard']['Card']['expiresAt'] ?? null))->maxLength(7)->isString()->isRequired(); //String contendo ano e mês de expiração do cartão. YYYY-MM
                $validate->set('PaymentMethodCreditCard.Card.cvv', ($charge['PaymentMethodCreditCard']['Card']['cvv'] ?? null))->maxLength(4)->isString()->isRequired(); //Código de segurança.

                $validate->set('PaymentMethodCreditCard.Antifraud.ip', ($charge['PaymentMethodCreditCard']['Antifraud']['ip'] ?? null))->maxLength(15)->isString()->isRequired(); //IPv4 do cliente.
                $validate->set('PaymentMethodCreditCard.Antifraud.sessionId', ($charge['PaymentMethodCreditCard']['Antifraud']['sessionId'] ?? null))->maxLength(255)->isString()->isRequired(); //SessionId gerado.

                $validate->set('PaymentMethodCreditCard.Link.minInstallment', ($charge['PaymentMethodCreditCard']['Link']['minInstallment'] ?? null))->maxLength(11)->isInteger(); //Mínimo de vezes que pode ser parcelado.
                $validate->set('PaymentMethodCreditCard.Link.maxInstallment', ($charge['PaymentMethodCreditCard']['Link']['maxInstallment'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Máximo de vezes que pode ser parcelado.

                $validate->set('PaymentMethodCreditCard.cardOperatorId', ($charge['PaymentMethodCreditCard']['cardOperatorId'] ?? null))->maxLength(30)->isString(); //Operadora, caso não informado será utilizada a ordem de prioridade definida no sistema.
                $validate->set('PaymentMethodCreditCard.preAuthorize', ($charge['PaymentMethodCreditCard']['preAuthorize'] ?? null))->isBoolean(); //Caso enviado como true a transação não será capturada automaticamente na operadora.
                $validate->set('PaymentMethodCreditCard.qtdInstallments', ($charge['PaymentMethodCreditCard']['qtdInstallments'] ?? null))->maxLength(11)->isInteger(); //Caso enviado como true a transação não será capturada automaticamente na operadora.
            }

            if (count($charge['PaymentMethodBoleto'] ?? [])) {
                $validate->set('PaymentMethodBoleto.fine', ($charge['PaymentMethodBoleto']['fine'] ?? null))->maxLength(11)->isInteger(); //Percentual de multa, com dois decimais sem o separador.
                $validate->set('PaymentMethodBoleto.interest', ($charge['PaymentMethodBoleto']['interest'] ?? null))->maxLength(11)->isInteger(); //Percentual de juros, com dois decimais sem o separador.
                $validate->set('PaymentMethodBoleto.instructions', ($charge['PaymentMethodBoleto']['instructions'] ?? null))->maxLength(255)->isString(); //Instruções do boleto.
                $validate->set('PaymentMethodBoleto.deadlineDays', ($charge['PaymentMethodBoleto']['deadlineDays'] ?? null))->maxLength(11)->isInteger(); //Quantidade de dias que o boleto pode ser pago após o vencimento.

                $validate->set('PaymentMethodBoleto.Discount.qtdDaysBeforePayDay', ($charge['PaymentMethodBoleto']['Discount']['qtdDaysBeforePayDay'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Quantidade de dias que o desconto será válido. Valores válidos de 0 a 20.
                $validate->set('PaymentMethodBoleto.Discount.type', ($charge['PaymentMethodBoleto']['Discount']['type'] ?? null))->maxLength(255)->isString()->isRequired(); //Define o tipo de desconto [percent|fixed]
                $validate->set('PaymentMethodBoleto.Discount.value', ($charge['PaymentMethodBoleto']['Discount']['value'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Valor do desconto em centavos.
            }

            if (count($charge['PaymentMethodPix'] ?? [])) {
                $validate->set('PaymentMethodPix.fine', ($charge['PaymentMethodPix']['fine'] ?? null))->maxLength(11)->isInteger(); //Percentual de multa, com dois decimais sem o separador.
                $validate->set('PaymentMethodPix.interest', ($charge['PaymentMethodPix']['interest'] ?? null))->maxLength(11)->isInteger(); //Percentual de juros, com dois decimais sem o separador.
                $validate->set('PaymentMethodPix.instructions', ($charge['PaymentMethodPix']['instructions'] ?? null))->maxLength(255)->isString(); //Instruções do QR Code Pix.

                $validate->set('PaymentMethodPix.Deadline.type', ($charge['PaymentMethodPix']['Deadline']['type'] ?? null))->maxLength(255)->isString(); //Tipo da expiração do QR Code. [days]
                $validate->set('PaymentMethodPix.Deadline.value', ($charge['PaymentMethodPix']['Deadline']['value'] ?? null))->maxLength(11)->isInteger(); //Valor da expiração do QR Code (em dias ou minutos).

                $validate->set('PaymentMethodPix.Discount.qtdDaysBeforePayDay', ($charge['PaymentMethodPix']['Discount']['qtdDaysBeforePayDay'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Quantidade de dias que o desconto será válido. Valores válidos de 0 a 20.
                $validate->set('PaymentMethodPix.Discount.type', ($charge['PaymentMethodPix']['Discount']['type'] ?? null))->maxLength(255)->isString()->isRequired(); //Define o tipo de desconto [percent|fixed]
                $validate->set('PaymentMethodPix.Discount.value', ($charge['PaymentMethodPix']['Discount']['value'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Valor do desconto em centavos.
            }

            if (count($charge['InvoiceConfig'] ?? [])) {
                $validate->set('InvoiceConfig.description', ($charge['InvoiceConfig']['description'] ?? null))->isString()->isRequired(); //Descrição da nota fiscal.
                $validate->set('InvoiceConfig.type', ($charge['InvoiceConfig']['type'] ?? null))->maxLength(255)->isString()->isRequired(); //Define se a emissão será uma por transação ou uma para a assinatura toda. [onePerTransaction]
                $validate->set('InvoiceConfig.createOn', ($charge['InvoiceConfig']['createOn'] ?? null))->maxLength(255)->isString()->isRequired(); //Define o momento que a nota fiscal será gerada.
                $validate->set('InvoiceConfig.qtdDaysBeforePayDay', ($charge['InvoiceConfig']['qtdDaysBeforePayDay'] ?? null))->maxLength(11)->isInteger(); //Quantidade de dias a ser considerado se o createOn for daysBeforePayDay.
                $validate->set('InvoiceConfig.galaxPaySubAccountId', ($charge['InvoiceConfig']['galaxPaySubAccountId'] ?? null))->maxLength(11)->isInteger(); //GalaxPayAccount.id - Conta Galax Pay referente a nota fiscal. Utilizada quando os pagamentos são realizados para uma conta matriz e a nota tem que ser emitida para uma filial.
            }

            $validate->validate();
        } catch (Exception $ex) {
            throw new Exception(\nl2br($ex->getMessage()));
        }
    }
}