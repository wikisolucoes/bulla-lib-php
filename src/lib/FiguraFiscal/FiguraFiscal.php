<?php

namespace Bulla\lib\FiguraFiscal;

use \Curl\Curl;
use \Exception;
use \stdClass;

/**
 * [REST]
 * Objeto responsável por consultar informações da Figura Fiscal API.
 *
 * @author Felipe Alves <felipe@wikisolucoes.com.br>
 */
class FiguraFiscal
{

    private $URL;
    private $ID;
    private $CNPJ;
    private $TOKEN_API;

    public function __construct(int $id, string $cnpj, string $token_api)
    {
        $this->URL = getenv('FIGURA_FISCAL_URL') ? getenv('FIGURA_FISCAL_URL') : $_ENV['FIGURA_FISCAL_URL'];
        $this->ID = $id;
        $this->CNPJ = $cnpj;
        $this->TOKEN_API = $token_api;
    }

    /**
     * [GET] Consulta Atualizações De Produtos
     *
     * Consulta Informações Tributárias Alteradas Em Uma Determinada Data
     *
     * @param string $data
     * @return array
     */
    public function consultaAtualizacoes(string $date): stdClass
    {
        $url = $this->URL . '/atualizacoes/' . $this->ID . '/' . $this->CNPJ . '/' . $this->TOKEN_API;

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->get($url, ['date' => $date]);

        $response = $curl->response;

        if (isset($response->mensagem)) {
            if ($response->mensagem == 'sucesso' && (!empty($response->tributos) || !empty($response->produtos_troca_segmento))) {
                return $response;
            } else {
                throw new Exception($response->mensagem);
            }
        } else {
            throw new Exception("Falha na consulta!");
        }

        if ($curl->error) {
            throw new Exception("Falha na consulta! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
        }
    }

    /**
     * [GET] Consulta EAN Em Lote
     *
     * Consulta Informações Tributárias De Um Lote De Produtos
     *
     * @param array $data
     * @return array
     */
    public function consultaEanLote(array $data): array
    {
        $url = $this->URL . '/revisao/' . $this->ID . '/' . $this->CNPJ . '/' . $this->TOKEN_API;

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post($url, $data);

        $response = $curl->response;

        if (isset($response->mensagem)) {
            if ($response->mensagem == 'sucesso' && !is_null($response->tributos)) {
                return $response->tributos;
            } else {
                throw new Exception($response->mensagem);
            }
        } else {
            throw new Exception("Falha na consulta!");
        }

        if ($curl->error) {
            throw new Exception("Falha na consulta! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
        }
    }

    /**
     * [GET] Consulta EAN
     *
     * Consulta Informações Tributárias do Produto
     *
     * @param string $codEan
     * @param string $descricao
     */
    public function consultaEan(string $codEan, string $descricao): stdClass
    {
        $url = $this->URL . '/consulta-ean/' . $this->ID . '/' . $this->CNPJ . '/' . $this->TOKEN_API;

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->get($url, [
            'ean' => $codEan,
            'descricao' => $descricao,
        ]);

        $response = $curl->response;

        if (isset($response->mensagem)) {
            if ($response->mensagem == 'sucesso' && !is_null($response->tributos)) {
                return is_array($response->tributos) ? array_shift($response->tributos) : $response->tributos;
            } else {
                throw new Exception($response->mensagem);
            }
        } else {
            throw new Exception("Falha na consulta!");
        }

        if ($curl->error) {
            throw new Exception("Falha na consulta! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
        }
    }

    /**
     * [GET] Consulta NCM
     *
     * Consulta Informações Tributárias do segmento de produtos do NCM
     *
     * @param string $ncm
     */
    public function consultaNCM(string $ncm): stdClass
    {
        $url = $this->URL . '/consulta-ncm/' . $this->ID . '/' . $this->CNPJ . '/' . $this->TOKEN_API;

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->get($url, [
            'ncm' => $ncm,
        ]);

        $response = $curl->response;

        if (isset($response->mensagem)) {
            if ($response->mensagem == 'sucesso' && !is_null($response->tributos)) {
                return is_array($response->tributos) ? array_shift($response->tributos) : $response->tributos;
            } else {
                throw new Exception($response->mensagem);
            }
        } else {
            throw new Exception("Falha na consulta!");
        }

        if ($curl->error) {
            throw new Exception("Falha na consulta! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
        }
    }

    /**
     * [GET] Consulta Atualizações por Segmento
     *
     * Consulta Informações Tributárias que foram atualizadas por segmento
     *
     * @param string $date (DD-MM-AAAA)
     */
    public function atualizacoes(string $date): stdClass
    {
        $url = $this->URL . '/atualizacoes/' . $this->ID . '/' . $this->CNPJ . '/' . $this->TOKEN_API;

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->get($url, [
            'date' => $date,
        ]);

        $response = $curl->response;

        if (isset($response->mensagem)) {
            if ($response->mensagem == 'sucesso' && !is_null($response->tributos)) {
                return is_array($response->tributos) ? array_shift($response->tributos) : $response->tributos;
            } else {
                throw new Exception($response->mensagem);
            }
        } else {
            throw new Exception("Falha na consulta!");
        }

        if ($curl->error) {
            throw new Exception("Falha na consulta! \n Error - {$curl->errorCode}: {$curl->errorMessage}");
        }
    }

}