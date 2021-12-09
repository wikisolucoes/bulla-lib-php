<?php

namespace Bulla\lib\GalaxPay;

use Bulla\lib\Validation;
use \Curl\Curl;
use \Exception;
use \stdClass;

/**
 * [REST] Endpoint /subscription
 * Objeto responsável pelo cadastro de assinaturas e contratos
 *
 * @author Felipe Alves <felipe@wikisolucoes.com.br>
 */
class Assinaturas extends Auth
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [GET] Retornar listagem de assinaturas/contratos
     *
     * @param array $params
     * @param string $order
     * @param int $limit
     * @param int $offset
     *
     * @return array $subscriptions
     */
    public function get(array $params = [], string $order = 'createdAt.asc', int $limit = 100, int $offset = 0): array
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/subscriptions';

            $params['order'] = $order;
            $params['limit'] = $limit;
            $params['startAt'] = $offset;

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->get($url, $params);

            $response = $curl->response;

            if (isset($response->Subscriptions)) {
                return $response->Subscriptions;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao buscar assinatura! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao buscar assinaturas! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [POST] Criar um nova assinatura/contrato
     *
     * @param array $subscription
     * @param bool $withPlan Criar assinatura com plano?
     *
     * @return stdClass $subscription
     */
    public function create(array $subscription, bool $withPlan = false): stdClass
    {
        try {
            $this->validate($subscription, $withPlan);

            $this->login();

            $url = $this->getApiHost() . '/subscriptions';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->post($url, $subscription);

            $response = $curl->response;

            if (isset($response->Subscription)) {
                return $response->Subscription;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao criar assinatura! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao criar assinatura! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Editar uma assinatura/contrato existente
     *
     * @param array $subscription
     *
     * @return stdClass $subscription
     */
    public function edit(array $subscription): stdClass
    {
        try {
            $this->validate($subscription);

            $this->login();

            $url = $this->getApiHost() . '/subscriptions/' . $subscription['myId'] . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url, $subscription);

            $response = $curl->response;

            if (isset($response->Subscription)) {
                return $response->Subscription;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao editar assinatura! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao editar assinatura! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [POST] Método utilizado para acrescentar uma transação manualmente dentro de uma assinatura/contrato.
     *
     * @param int $subscriptionId
     * @param array $transaction
     *
     * @return stdClass $transaction
     */
    public function addTransaction(int $subscriptionId, array $transaction): stdClass
    {
        try {
            $this->validateTransaction($transaction);

            $this->login();

            $url = $this->getApiHost() . '/transactions/' . $subscriptionId . '/myId/add';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->post($url, $transaction);

            $response = $curl->response;

            if (isset($response->Transaction)) {
                return $response->Transaction;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao criar transação! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao criar transação! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Método utilizado para tentar novamente realizar a captura da transação de uma assinatura/contrato.
     *
     * @param int $transactionId
     *
     * @return stdClass $transaction
     */
    public function retryTransaction(int $transactionId): stdClass
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/transactions/' . $transactionId . '/myId/retry';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url);

            $response = $curl->response;

            if (isset($response->Transaction)) {
                return $response->Transaction;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao tentar captar assinatura! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao tentar captar assinatura! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Método utilizado para realizar o estorno da transação de uma assinatura/contrato.
     *
     * @param int $transactionId
     *
     * @return stdClass $transaction
     */
    public function reverseTransaction(int $transactionId): stdClass
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/transactions/' . $transactionId . '/myId/reverse';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url);

            $response = $curl->response;

            if (isset($response->Transaction)) {
                return $response->Transaction;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao tentar estornar assinatura! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao tentar estornar assinatura! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [PUT] Método utilizado para realizar a captura de uma transação autorizada.
     *
     * @param int $transactionId
     *
     * @return stdClass $transaction
     */
    public function captureTransaction(int $transactionId): stdClass
    {
        try {
            $this->login();

            $url = $this->getApiHost() . '/transactions/' . $transactionId . '/myId/capture';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->put($url);

            $response = $curl->response;

            if (isset($response->Transaction)) {
                return $response->Transaction;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao tentar estornar assinatura! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao tentar estornar assinatura! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [DELETE] Excluir uma assinatura/contrato
     *
     * @param array $subscription
     *
     * @return boolean $type
     */
    public function delete(array $subscription): bool
    {
        try {
            if (!isset($subscription['myId'])) {
                throw new Exception("Erro ao excluir assinatura! \n Error: ID não informado!");
            }

            $this->login();

            $url = $this->getApiHost() . '/subscriptions/' . $subscription['myId'] . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->delete($url);

            $response = $curl->response;

            if (isset($response->type)) {
                return $response->type;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao excluir assinatura! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao excluir assinatura! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * [DELETE] Método utilizado para cancelar uma transação de uma assinatura/contrato.
     *
     * @param array $transaction
     *
     * @return boolean $type
     */
    public function deleteTransaction(array $transaction): bool
    {
        try {
            if (!isset($transaction['myId'])) {
                throw new Exception("Erro ao excluir transação! \n Error: ID não informado!");
            }

            $this->login();

            $url = $this->getApiHost() . '/transactions/' . $transaction['myId'] . '/myId';

            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Authorization', $this->getTokenType() . ' ' . $this->getToken());
            $curl->delete($url);

            $response = $curl->response;

            if (isset($response->type)) {
                return $response->type;
            } else if (isset($response->error)) {
                throw new Exception("Erro ao excluir transação! \n" . $this->parseError($response->error));
            }

            if ($curl->error) {
                throw new Exception("Erro ao excluir transação! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Validar os campos do cadastro de assinaturas/contratos
     *
     * @param array $subscription
     * @param bool $withPlan
     */
    private function validate(array $subscription, bool $withPlan = false): void
    {
        try {
            $validate = new Validation();

            $validate->set('myId', ($subscription['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
            $validate->set('firstPayDayDate', ($subscription['firstPayDayDate'] ?? null))->maxLength(14)->isString()->isRequired(); //Data do primeiro pagamento.
            $validate->set('additionalInfo', ($subscription['additionalInfo'] ?? null))->isString(); //Texto livre dedicado a informações adicionais internas.
            $validate->set('mainPaymentMethodId', ($subscription['mainPaymentMethodId'] ?? null))->maxLength(255)->isString(); //PaymentMethod.id => Id do pagamento principal.

            if ($withPlan) {
                $validate->set('planMyId', ($subscription['planMyId'] ?? null))->maxLength(255)->isString()->isRequired(); //Plan.myId => ID do plano no seu sistema.
            } else {
                $validate->set('value', ($subscription['value'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Preço em centavos.
                $validate->set('quantity', ($subscription['quantity'] ?? null))->maxLength(3)->isInteger()->isRequired(); //Para indeterminada envie 0.
                $validate->set('periodicity', ($subscription['periodicity'] ?? null))->maxLength(255)->isString()->isRequired(); //Periodicidade dos pagamentos. [weekly|biweekly|monthly|bimonthly|quarterly|biannual|yearly]

                if (count(($subscription['Transactions'] ?? []))) {
                    foreach ($subscription['Transactions'] as $transaction) {
                        $validate->set('Transactions.myId', ($transaction['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
                        $validate->set('Transactions.installment', ($transaction['installment'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Parcela.
                        $validate->set('Transactions.value', ($transaction['value'] ?? null))->maxLength(11)->isInteger(); //Preço em centavos.
                        $validate->set('Transactions.payday', ($transaction['payday'] ?? null))->maxLength(10)->isString(); //Data de vencimento do pagamento.
                        $validate->set('Transactions.payedOutsideGalaxPay', ($transaction['payedOutsideGalaxPay'] ?? null))->isBoolean(); //Define se a cobrança foi paga fora do sistema do Galax Pay.
                        $validate->set('Transactions.additionalInfo', ($transaction['additionalInfo'] ?? null))->isString(); //Texto para informações adicionais sobre a transação.

                        if (count(($transaction['InvoiceConfig'] ?? []))) {
                            $validate->set('Transactions.InvoiceConfig.description', ($transaction['InvoiceConfig']['description'] ?? null))->isString(); //Descrição da nota fiscal.
                        }
                    }
                }
            }

            $validate->set('Customer.myId', ($subscription['Customer']['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
            $validate->set('Customer.name', ($subscription['Customer']['name'] ?? null))->maxLength(255)->isString()->isRequired(); //Nome ou razão social do cliente.
            $validate->set('Customer.document', ($subscription['Customer']['document'] ?? null))->maxLength(255)->isString()->isRequired(); //CPF OU CNPJ do cliente. Apenas números.
            $validate->set('Customer.invoiceHoldIss', ($subscription['Customer']['invoiceHoldIss'] ?? null))->maxLength(255)->isString(); //Se reterá ISS na nota fiscal ou não.
            $validate->set('Customer.municipalDocument', ($subscription['Customer']['municipalDocument'] ?? null))->maxLength(255)->isString(); //Inscrição municipal do cliente.

            if (count(($subscription['Customer']['emails'] ?? []))) {
                foreach ($subscription['Customer']['emails'] as $k => $email) {
                    $validate->set('Customer.email', $email)->maxLength(255)->isString()->isRequired(); //Emails do cliente.
                }
            }

            if (count(($subscription['Customer']['phones'] ?? []))) {
                foreach ($subscription['Customer']['phones'] as $k => $phone) {
                    $validate->set('Customer.phone', $phone)->maxLength(11)->isInteger(); //Telefones do cliente.
                }
            }

            if (count(($subscription['Customer']['Address'] ?? []))) {
                $validate->set('Customer.Address.zipCode', ($subscription['Customer']['Address']['zipCode'] ?? null))->maxLength(8)->isString()->isRequired(); //CEP. Informe apenas números.
                $validate->set('Customer.Address.street', ($subscription['Customer']['Address']['street'] ?? null))->maxLength(255)->isString()->isRequired(); //Logradouro.
                $validate->set('Customer.Address.number', ($subscription['Customer']['Address']['number'] ?? null))->maxLength(255)->isString()->isRequired(); //Número.
                $validate->set('Customer.Address.complement', ($subscription['Customer']['Address']['complement'] ?? null))->maxLength(255)->isString(); //Complemento.
                $validate->set('Customer.Address.neighborhood', ($subscription['Customer']['Address']['neighborhood'] ?? null))->maxLength(255)->isString()->isRequired(); //Bairro.
                $validate->set('Customer.Address.city', ($subscription['Customer']['Address']['city'] ?? null))->maxLength(255)->isString()->isRequired(); //Cidade.
                $validate->set('Customer.Address.state', ($subscription['Customer']['Address']['state'] ?? null))->maxLength(2)->isString()->isRequired(); //Estado.
            }

            if (count(($subscription['PaymentMethodCreditCard'] ?? []))) {
                $validate->set('PaymentMethodCreditCard.Card.myId', ($subscription['PaymentMethodCreditCard']['Card']['myId'] ?? null))->maxLength(255)->isString(); //Id referente no seu sistema, para salvar no Galax Pay.
                $validate->set('PaymentMethodCreditCard.Card.number', ($subscription['PaymentMethodCreditCard']['Card']['number'] ?? null))->maxLength(30)->isString()->isRequired(); //Número do cartão.
                $validate->set('PaymentMethodCreditCard.Card.holder', ($subscription['PaymentMethodCreditCard']['Card']['holder'] ?? null))->maxLength(30)->isString()->isRequired(); //Nome do portador.
                $validate->set('PaymentMethodCreditCard.Card.expiresAt', ($subscription['PaymentMethodCreditCard']['Card']['expiresAt'] ?? null))->maxLength(7)->isString()->isRequired(); //String contendo ano e mês de expiração do cartão. YYYY-MM
                $validate->set('PaymentMethodCreditCard.Card.cvv', ($subscription['PaymentMethodCreditCard']['Card']['cvv'] ?? null))->maxLength(4)->isString()->isRequired(); //Código de segurança.

                $validate->set('PaymentMethodCreditCard.Antifraud.ip', ($subscription['PaymentMethodCreditCard']['Antifraud']['ip'] ?? null))->maxLength(15)->isString()->isRequired(); //IPv4 do cliente.
                $validate->set('PaymentMethodCreditCard.Antifraud.sessionId', ($subscription['PaymentMethodCreditCard']['Antifraud']['sessionId'] ?? null))->maxLength(255)->isString()->isRequired(); //SessionId gerado.

                $validate->set('PaymentMethodCreditCard.cardOperatorId', ($subscription['PaymentMethodCreditCard']['cardOperatorId'] ?? null))->maxLength(30)->isString(); //Operadora, caso não informado será utilizada a ordem de prioridade definida no sistema.
                $validate->set('PaymentMethodCreditCard.preAuthorize', ($subscription['PaymentMethodCreditCard']['preAuthorize'] ?? null))->isBoolean(); //Caso enviado como true a transação não será capturada automaticamente na operadora.
            }

            if (count(($subscription['PaymentMethodBoleto'] ?? []))) {
                $validate->set('PaymentMethodBoleto.fine', ($subscription['PaymentMethodBoleto']['fine'] ?? null))->maxLength(11)->isInteger(); //Percentual de multa, com dois decimais sem o separador.
                $validate->set('PaymentMethodBoleto.interest', ($subscription['PaymentMethodBoleto']['interest'] ?? null))->maxLength(11)->isInteger(); //Percentual de juros, com dois decimais sem o separador.
                $validate->set('PaymentMethodBoleto.instructions', ($subscription['PaymentMethodBoleto']['instructions'] ?? null))->maxLength(255)->isString(); //Instruções do boleto.
                $validate->set('PaymentMethodBoleto.deadlineDays', ($subscription['PaymentMethodBoleto']['deadlineDays'] ?? null))->maxLength(11)->isInteger(); //Quantidade de dias que o boleto pode ser pago após o vencimento.

                $validate->set('PaymentMethodBoleto.Discount.qtdDaysBeforePayDay', ($subscription['PaymentMethodBoleto']['Discount']['qtdDaysBeforePayDay'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Quantidade de dias que o desconto será válido. Valores válidos de 0 a 20.
                $validate->set('PaymentMethodBoleto.Discount.type', ($subscription['PaymentMethodBoleto']['Discount']['type'] ?? null))->maxLength(255)->isString()->isRequired(); //Define o tipo de desconto [percent|fixed]
                $validate->set('PaymentMethodBoleto.Discount.value', ($subscription['PaymentMethodBoleto']['Discount']['value'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Valor do desconto em centavos.
            }

            if (count(($subscription['PaymentMethodPix'] ?? []))) {
                $validate->set('PaymentMethodPix.fine', ($subscription['PaymentMethodPix']['fine'] ?? null))->maxLength(11)->isInteger(); //Percentual de multa, com dois decimais sem o separador.
                $validate->set('PaymentMethodPix.interest', ($subscription['PaymentMethodPix']['interest'] ?? null))->maxLength(11)->isInteger(); //Percentual de juros, com dois decimais sem o separador.
                $validate->set('PaymentMethodPix.instructions', ($subscription['PaymentMethodPix']['instructions'] ?? null))->maxLength(255)->isString(); //Instruções do QR Code Pix.

                $validate->set('PaymentMethodPix.Deadline.type', ($subscription['PaymentMethodPix']['Deadline']['type'] ?? null))->maxLength(255)->isString(); //Tipo da expiração do QR Code. [days]
                $validate->set('PaymentMethodPix.Deadline.value', ($subscription['PaymentMethodPix']['Deadline']['value'] ?? null))->maxLength(11)->isInteger(); //Valor da expiração do QR Code (em dias ou minutos).

                $validate->set('PaymentMethodPix.Discount.qtdDaysBeforePayDay', ($subscription['PaymentMethodPix']['Discount']['qtdDaysBeforePayDay'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Quantidade de dias que o desconto será válido. Valores válidos de 0 a 20.
                $validate->set('PaymentMethodPix.Discount.type', ($subscription['PaymentMethodPix']['Discount']['type'] ?? null))->maxLength(255)->isString()->isRequired(); //Define o tipo de desconto [percent|fixed]
                $validate->set('PaymentMethodPix.Discount.value', ($subscription['PaymentMethodPix']['Discount']['value'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Valor do desconto em centavos.
            }

            if (count(($subscription['InvoiceConfig'] ?? []))) {
                $validate->set('InvoiceConfig.description', ($subscription['InvoiceConfig']['description'] ?? null))->isString()->isRequired(); //Descrição da nota fiscal.
                $validate->set('InvoiceConfig.type', ($subscription['InvoiceConfig']['type'] ?? null))->maxLength(255)->isString()->isRequired(); //Define se a emissão será uma por transação ou uma para a assinatura toda. [onePerTransaction]
                $validate->set('InvoiceConfig.createOn', ($subscription['InvoiceConfig']['createOn'] ?? null))->maxLength(255)->isString()->isRequired(); //Define o momento que a nota fiscal será gerada.
                $validate->set('InvoiceConfig.qtdDaysBeforePayDay', ($subscription['InvoiceConfig']['qtdDaysBeforePayDay'] ?? null))->maxLength(11)->isInteger(); //Quantidade de dias a ser considerado se o createOn for daysBeforePayDay.
                $validate->set('InvoiceConfig.galaxPaySubAccountId', ($subscription['InvoiceConfig']['galaxPaySubAccountId'] ?? null))->maxLength(11)->isInteger(); //GalaxPayAccount.id - Conta Galax Pay referente a nota fiscal. Utilizada quando os pagamentos são realizados para uma conta matriz e a nota tem que ser emitida para uma filial.
            }

            $validate->validate();
        } catch (Exception $ex) {
            throw new Exception(\nl2br($ex->getMessage()));
        }
    }

    /**
     * Validar os campos da transação
     *
     * @param array $transaction
     */
    private function validateTransaction(array $transaction): void
    {
        try {
            $validate = new Validation();

            $validate->set('myId', ($transaction['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
            $validate->set('value', ($transaction['value'] ?? null))->maxLength(11)->isInteger()->isRequired(); //Preço em centavos.
            $validate->set('payday', ($transaction['payday'] ?? null))->maxLength(10)->isString()->isRequired(); //Data de vencimento do pagamento.
            $validate->set('payedOutsideGalaxPay', ($transaction['payedOutsideGalaxPay'] ?? null))->isBoolean(); //Define se a cobrança foi paga fora do sistema do Galax Pay.
            $validate->set('additionalInfo', ($transaction['additionalInfo'] ?? null))->isString(); //Texto para informações adicionais sobre a transação.

            if (count(($transaction['PaymentMethodCreditCard'] ?? []))) {
                $validate->set('PaymentMethodCreditCard.Card.myId', ($transaction['PaymentMethodCreditCard']['Card']['myId'] ?? null))->maxLength(255)->isString()->isRequired(); //Id referente no seu sistema, para salvar no Galax Pay.
                $validate->set('PaymentMethodCreditCard.Card.number', ($transaction['PaymentMethodCreditCard']['Card']['number'] ?? null))->maxLength(30)->isString()->isRequired(); //Número do cartão.
                $validate->set('PaymentMethodCreditCard.Card.holder', ($transaction['PaymentMethodCreditCard']['Card']['holder'] ?? null))->maxLength(30)->isString()->isRequired(); //Nome do portador.
                $validate->set('PaymentMethodCreditCard.Card.expiresAt', ($transaction['PaymentMethodCreditCard']['Card']['expiresAt'] ?? null))->maxLength(7)->isString()->isRequired(); //String contendo ano e mês de expiração do cartão. YYYY-MM
                $validate->set('PaymentMethodCreditCard.Card.cvv', ($transaction['PaymentMethodCreditCard']['Card']['cvv'] ?? null))->maxLength(4)->isString()->isRequired(); //Código de segurança.

                $validate->set('PaymentMethodCreditCard.Antifraud.ip', ($transaction['PaymentMethodCreditCard']['Antifraud']['ip'] ?? null))->maxLength(15)->isString()->isRequired(); //IPv4 do cliente.
                $validate->set('PaymentMethodCreditCard.Antifraud.sessionId', ($transaction['PaymentMethodCreditCard']['Antifraud']['sessionId'] ?? null))->maxLength(255)->isString()->isRequired(); //SessionId gerado.

                $validate->set('PaymentMethodCreditCard.cardOperatorId', ($transaction['PaymentMethodCreditCard']['cardOperatorId'] ?? null))->maxLength(30)->isString(); //Operadora, caso não informado será utilizada a ordem de prioridade definida no sistema.
                $validate->set('PaymentMethodCreditCard.preAuthorize', ($transaction['PaymentMethodCreditCard']['preAuthorize'] ?? null))->isBoolean(); //Caso enviado como true a transação não será capturada automaticamente na operadora.
            }

            if (count(($transaction['InvoiceConfig'] ?? []))) {
                $validate->set('InvoiceConfig.description', ($transaction['InvoiceConfig']['description'] ?? null))->isString()->isRequired(); //Descrição da nota fiscal.
                $validate->set('InvoiceConfig.type', ($transaction['InvoiceConfig']['type'] ?? null))->maxLength(255)->isString()->isRequired(); //Define se a emissão será uma por transação ou uma para a assinatura toda. [onePerTransaction]
                $validate->set('InvoiceConfig.createOn', ($transaction['InvoiceConfig']['createOn'] ?? null))->maxLength(255)->isString()->isRequired(); //Define o momento que a nota fiscal será gerada.
                $validate->set('InvoiceConfig.qtdDaysBeforePayDay', ($transaction['InvoiceConfig']['qtdDaysBeforePayDay'] ?? null))->maxLength(11)->isInteger(); //Quantidade de dias a ser considerado se o createOn for daysBeforePayDay.
                $validate->set('InvoiceConfig.galaxPaySubAccountId', ($transaction['InvoiceConfig']['galaxPaySubAccountId'] ?? null))->maxLength(11)->isInteger(); //GalaxPayAccount.id - Conta Galax Pay referente a nota fiscal. Utilizada quando os pagamentos são realizados para uma conta matriz e a nota tem que ser emitida para uma filial.
            }

            $validate->validate();
        } catch (Exception $ex) {
            throw new Exception(\nl2br($ex->getMessage()));
        }
    }
}