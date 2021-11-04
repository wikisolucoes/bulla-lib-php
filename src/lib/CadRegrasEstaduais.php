<?php

namespace Bulla\lib;

use Bulla\database\DB;
use Bulla\lib\Validation;
use \Exception;
use \PDO;

class CadRegrasEstaduais
{

    private $db;
    private $table = 'CadRegrasEstaduais';
    private $filters = [];

    public function __construct()
    {
        $this->db = new DB();
        $this->initFilters();
    }

    public function get()
    {
        try {
            $this->validateFilters();

            $defaultRegimeTributario = $this->db->row("SELECT * FROM CadRegimeTributario WHERE descricao = 'TODOS'");

            $filters = $this->filters;

            $fields = ['CadRegrasEstaduais.id', 'idRegimeTributario', 'codeNcmInicial', 'codeNcmFinal', 'nivel_ncm', 'CadRegrasEstaduais.situacao',
                'CadCodCstIcms.id as idCstIcms', 'CadTiposAliqIcms.aliquota_padrao', 'CadModalidadeBaseICMS.descricao as mod_base_calc'];
            $where = [];

            array_push($where, "(idRegimeTributario = " . $filters['idRegimeTributario'] . " OR idRegimeTributario = " . $defaultRegimeTributario['id'] . ")");
            unset($filters['idRegimeTributario']);

            array_push($where, "codeNcmInicial <= '" . $filters['codeNcmInicial'] . "'");
            unset($filters['codeNcmInicial']);

            array_push($where, "codeNcmFinal >= '" . $filters['codeNcmFinal'] . "'");
            unset($filters['codeNcmFinal']);

            foreach ($filters as $name => $value) {
                if (!is_null($value)) {
                    array_push($fields, "{$this->table}.{$name}");
                    array_push($where, "({$this->table}.{$name} = '{$value}' OR {$this->table}.{$name} is NULL)");
                }
            }

            $join = [
                "JOIN CadCodCstIcms ON CadCodCstIcms.id = {$this->table}.idCodCstIcms",
                "JOIN CadTiposAliqIcms ON CadTiposAliqIcms.id = {$this->table}.idTipoAliqIcms",
                "JOIN CadModalidadeBaseICMS ON CadModalidadeBaseICMS.id = {$this->table}.idModBaseIcms",
            ];

            $fields = implode(', ', $fields);
            $where = implode(' AND ', $where);
            $join = implode(' ', $join);
            $sql = "SELECT {$fields} FROM {$this->table} {$join} WHERE {$where} AND {$this->table}.situacao = 'A' ORDER BY nivel_ncm ASC";
            //\Bulla\Helper\Output::print_log("SQL: {$sql}", "[database]");
            $rows = $this->db->query($sql);
            //Output::print_ln("RESULTADO:");
            //Output::print_array($rows);
            $idRegraEstadual = $this->sort($rows);

            if (!$idRegraEstadual) {
                throw new Exception('Regra Tributária Estadual não encontrada!');
            }

            return $this->getRegraById($idRegraEstadual);
        } catch (Exception $ex) {
            //Output::print_ln(nl2br($ex->getMessage()), true);
            throw new Exception($ex->getMessage());
        }
    }

    public function getRegraById($id = 0)
    {
        try {
            if (!$id) {
                throw new Exception('ID da regra não informado!');
            }

            $fields = implode(', ', [
                'CadRegrasEstaduais.*', 'CadCodCstIcms.id as idCstIcms',
                'CadTiposAliqIcms.aliquota_padrao as idAliqDife', 'CadTiposAliqIcms.id as idAliqDifeciad', 'CadModalidadeBaseICMS.descricao as mod_base_cal',
            ]);

            $join = implode(' ', [
                "JOIN CadCodCstIcms ON CadCodCstIcms.id = {$this->table}.idCodCstIcms",
                "JOIN CadTiposAliqIcms ON CadTiposAliqIcms.id = {$this->table}.idTipoAliqIcms",
                "JOIN CadModalidadeBaseICMS ON CadModalidadeBaseICMS.id = {$this->table}.idModBaseIcms",
            ]);

            $regra = $this->db->row("SELECT {$fields} FROM {$this->table} {$join} WHERE {$this->table}.id = :id", ['id' => $id], PDO::FETCH_OBJ);

            if (isset($regra->id)) {
                return $regra;
            }

            return false;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    private function sort($rows = [])
    {
        try {
            $filters = array_filter($this->filters, function ($value) {return !is_null($value) && $value !== '';});
            $keys = [];
            foreach ($rows as $row) {
                $match = 0;

                foreach ($filters as $filterName => $filterValue) { //Para cada filtro
                    if ($row[$filterName] == $filterValue) { //Se registro = filtro
                        $match++; //Registra ocorrencia
                    }
                }
                //Add array keys o ID do registro e a quantidade de ocorrencias
                $keys[$row['id']] = $match;
            }

            arsort($keys); //Ordenar array em ordem descrescente, mantendo o maior numero de ocorrencias primeiro

            //Output::print_ln("ORDENADO:");
            //Output::print_array($keys);

            //Retorna a chave do primeiro elemento, ou seja, regra com mais ocorrencias nos filtros
            return array_key_first($keys);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function setFilter($name = '', $value = '')
    {
        if (!strlen($name) or !strlen($value)) {
            throw new Exception('Informe um filtro válido!');
        }

        if (array_key_exists($name, $this->filters)) {
            $this->filters[$name] = $value;
        }
    }

    private function validateFilters()
    {
        try {
            $filters = array_filter($this->filters, function ($value) {return !is_null($value) && $value !== '';});

            $validate = new Validation();

            $validate->set('UF', (isset($filters['uf']) ? $filters['uf'] : null))->maxLength(2)->isString()->isRequired();
            $validate->set('Regime Tributário', (isset($filters['idRegimeTributario']) ? $filters['idRegimeTributario'] : null))->isInteger()->isRequired();
            $validate->set('Zona Franca', (isset($filters['zona_franca']) ? $filters['zona_franca'] : null))->maxLength(1)->isInteger()->isRequired();
            $validate->set('NCM Inicial', (isset($filters['codeNcmInicial']) ? $filters['codeNcmInicial'] : null))->maxLength(8)->isInteger()->isRequired();
            $validate->set('NCM Final', (isset($filters['codeNcmFinal']) ? $filters['codeNcmFinal'] : null))->maxLength(8)->isInteger()->isRequired();
            $validate->set('Código CEST', (isset($filters['idCodCest']) ? $filters['idCodCest'] : null))->maxLength(11)->isInteger();
            $validate->set('Lista Comercialização', (isset($filters['idListaComerc']) ? $filters['idListaComerc'] : null))->maxLength(11)->isInteger();
            $validate->set('Categoria', (isset($filters['idCategoria']) ? $filters['idCategoria'] : null))->maxLength(11)->isInteger();
            $validate->set('Princípio Ativo', (isset($filters['idPrincAtivo']) ? $filters['idPrincAtivo'] : null))->maxLength(11)->isInteger();
            $validate->set('Tag', (isset($filters['idTag']) ? $filters['idTag'] : null))->maxLength(11)->isInteger();
            $validate->set('Fornecedor', (isset($filters['idFornecedor']) ? $filters['idFornecedor'] : null))->maxLength(11)->isInteger();
            $validate->set('Fabricante', (isset($filters['idFabricante']) ? $filters['idFabricante'] : null))->maxLength(11)->isInteger();
            $validate->set('Registro MS', (isset($filters['idRegistro']) ? $filters['idRegistro'] : null))->maxLength(11)->isInteger();
            $validate->set('Tipo Lote', (isset($filters['idTipoLote']) ? $filters['idTipoLote'] : null))->maxLength(11)->isInteger();
            $validate->set('Produto', (isset($filters['idProduto']) ? $filters['idProduto'] : null))->maxLength(11)->isInteger();
            $validate->set('Subgrupo Nivel 3', (isset($filters['idSubgrupoNivel3']) ? $filters['idSubgrupoNivel3'] : null))->maxLength(11)->isInteger();
            $validate->set('Subgrupo Nivel 2', (isset($filters['idSubgrupoNivel2']) ? $filters['idSubgrupoNivel2'] : null))->maxLength(11)->isInteger();
            $validate->set('Subgrupo Nivel 1', (isset($filters['idSubgrupoNivel1']) ? $filters['idSubgrupoNivel1'] : null))->maxLength(11)->isInteger();
            $validate->set('Grupo', (isset($filters['idGrupo']) ? $filters['idGrupo'] : null))->maxLength(11)->isInteger();
            $validate->set('Nível NCM', (isset($filters['nivel_ncm']) ? $filters['nivel_ncm'] : null))->maxLength(11)->isInteger();
            $validate->set('Nome Município', (isset($filters['nome_municipio']) ? $filters['nome_municipio'] : null))->maxLength(255);
            $validate->set('IBGE Município', (isset($filters['ibge_municipio']) ? $filters['ibge_municipio'] : null))->maxLength(11)->isInteger();

            $validate->validate();
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    private function initFilters()
    {
        $this->filters['uf'] = null;
        $this->filters['idRegimeTributario'] = null;
        $this->filters['zona_franca'] = null;
        $this->filters['codeNcmInicial'] = null;
        $this->filters['codeNcmFinal'] = null;
        $this->filters['idCodCest'] = null;
        $this->filters['idListaComerc'] = null;
        $this->filters['idCategoria'] = null;
        $this->filters['idPrincAtivo'] = null;
        $this->filters['idTag'] = null;
        $this->filters['idFornecedor'] = null;
        $this->filters['idFabricante'] = null;
        $this->filters['idRegistro'] = null;
        $this->filters['idTipoLote'] = null;
        $this->filters['idProduto'] = null;
        $this->filters['idSubgrupoNivel3'] = null;
        $this->filters['idSubgrupoNivel2'] = null;
        $this->filters['idSubgrupoNivel1'] = null;
        $this->filters['idGrupo'] = null;
        $this->filters['nivel_ncm'] = null;
        $this->filters['nome_municipio'] = null;
        $this->filters['ibge_municipio'] = null;
    }
}
