<?php

namespace Bulla\lib;

use Bulla\database\DB;
use Bulla\lib\Validation;
use \Exception;
use \PDO;

class CadRegrasFederais
{

    private $db;
    private $table = 'CadRegrasFederais';
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

            $filters = $this->filters;

            $fields = ['id', 'idRegimeTributario', 'codeNcmInicial', 'codeNcmFinal', 'nivel_ncm'];
            $where = [];

            array_push($where, "idRegimeTributario = " . $filters['idRegimeTributario']);
            unset($filters['idRegimeTributario']);

            array_push($where, "codeNcmInicial >= '" . $filters['codeNcmInicial'] . "'");
            unset($filters['codeNcmInicial']);

            array_push($where, "codeNcmFinal <= '" . $filters['codeNcmFinal'] . "'");
            unset($filters['codeNcmFinal']);

            foreach ($filters as $name => $value) {
                if (!is_null($value)) {
                    array_push($fields, $name);
                    array_push($where, "({$name} = '{$value}' OR {$name} is NULL)");
                }
            }

            $fields = implode(', ', $fields);
            $where = implode(' AND ', $where);
            $sql = "SELECT {$fields} FROM {$this->table} WHERE {$where} AND situacao = 'A' ORDER BY nivel_ncm ASC";
            // Output::print_ln("SQL: {$sql}");
            $rows = $this->db->query($sql);
            //Output::print_ln("RESULTADO:");
            //Output::print_array($rows);

            $idRegraFederal = $this->sort($rows);

            if (!$idRegraFederal) {
                throw new Exception('Regra Tributária Federal não encontrada!');
            }

            return $this->getRegraById($idRegraFederal);
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

            $regra = $this->db->row("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id], PDO::FETCH_OBJ);

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

            $validate->set('UF', $filters['uf'])->maxLength(2)->isString();
            $validate->set('Regime Tributário', $filters['idRegimeTributario'])->isInteger()->isRequired();
            $validate->set('Zona Franca', $filters['zona_franca'])->maxLength(1)->isInteger()->isRequired();
            $validate->set('NCM Inicial', $filters['codeNcmInicial'])->maxLength(8)->isInteger()->isRequired();
            $validate->set('NCM Final', $filters['codeNcmFinal'])->maxLength(8)->isInteger()->isRequired();
            $validate->set('Código CEST', $filters['idCodCest'])->maxLength(11)->isInteger();
            $validate->set('Lista Comercialização', $filters['idListaComerc'])->maxLength(11)->isInteger();
            $validate->set('Categoria', $filters['idCategoria'])->maxLength(11)->isInteger();
            $validate->set('Princípio Ativo', $filters['idPrincAtivo'])->maxLength(11)->isInteger();
            $validate->set('Tag', $filters['idTag'])->maxLength(11)->isInteger();
            $validate->set('Fornecedor', $filters['idFornecedor'])->maxLength(11)->isInteger();
            $validate->set('Fabricante', $filters['idFabricante'])->maxLength(11)->isInteger();
            $validate->set('Registro MS', $filters['idRegistro'])->maxLength(11)->isInteger();
            $validate->set('Tipo Lote', $filters['idTipoLote'])->maxLength(11)->isInteger();
            $validate->set('Produto', $filters['idProduto'])->maxLength(11)->isInteger();
            $validate->set('Subgrupo Nivel 3', $filters['idSubgrupoNivel3'])->maxLength(11)->isInteger();
            $validate->set('Subgrupo Nivel 2', $filters['idSubgrupoNivel2'])->maxLength(11)->isInteger();
            $validate->set('Subgrupo Nivel 1', $filters['idSubgrupoNivel1'])->maxLength(11)->isInteger();
            $validate->set('Grupo', $filters['idGrupo'])->maxLength(11)->isInteger();
            $validate->set('Nível NCM', $filters['nivel_ncm'])->maxLength(11)->isInteger();
            $validate->set('Nome Município', $filters['nome_municipio'])->maxLength(255);
            $validate->set('IBGE Município', $filters['ibge_municipio'])->maxLength(11)->isInteger();

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