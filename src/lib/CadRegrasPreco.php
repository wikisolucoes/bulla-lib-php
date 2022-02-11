<?php

namespace Bulla\lib;

use Bulla\database\DB;
use Bulla\lib\Validation;
use \Exception;
use \PDO;

class CadRegrasPreco
{

    private $db;
    private $table = 'CadRegrasPreco';
    private $filters = [];

    public function __construct()
    {
        $this->db = new DB();
        $this->initFilters();
    }

    public function get($limit = 1)
    {
        try {
            $this->validateFilters();

            $filters = $this->filters;

            $where = [];
            array_push($where, "cod_barra = '" . $filters['cod_barra'] . "'");
            unset($filters['cod_barra']);

            if(is_array($filters['idTipoAliqIcms'])) {
                $where = implode(" OR idTipoAliqIcms = ", $filters['idTipoAliqIcms']);
                array_push($where, "(idTipoAliqIcms = {$where})");
                unset($filters['idTipoAliqIcms']);
            } else {
                array_push($where, "idTipoAliqIcms = " . $filters['idTipoAliqIcms']);
                unset($filters['idTipoAliqIcms']);
            }

            foreach ($filters as $name => $value) {
                if (!is_null($value)) {
                    array_push($where, "{$name} = '{$value}'");
                }
            }

            $where = implode(' AND ', $where);
            $sql = "SELECT * FROM {$this->table} WHERE {$where} AND situacao = 'A' LIMIT {$limit}";
            // Output::print_ln("SQL: {$sql}");
            $rows = $this->db->query($sql);
            //Output::print_ln("RESULTADO:");
            //Output::print_array($rows);

            $idRegraPreco = $this->sort($rows);

            if (!$idRegraPreco) {
                throw new Exception('Regra de Preço não encontrada!');
            }

            return $this->getRegraById($idRegraPreco);
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

            $validate->set('Código de Barras', (isset($filters['cod_barra']) ? $filters['cod_barra'] : null))->maxLength(255)->isString()->isRequired();
            $validate->set('Tipo Aliquota ICMS', (isset($filters['idTipoAliqIcms']) ? $filters['idTipoAliqIcms'] : null))->maxLength(11)->isInteger()->isRequired();
            $validate->set('ID Produto', (isset($filters['idProduto']) ? $filters['idProduto'] : null))->maxLength(11)->isInteger();
            $validate->set('Lista Comercialização', (isset($filters['idListaComerc']) ? $filters['idListaComerc'] : null))->maxLength(11)->isInteger();
            $validate->set('Fornecedor de Preço', (isset($filters['idProduto']) ? $filters['idProduto'] : null))->maxLength(11)->isInteger();

            $validate->validate();
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

    private function initFilters()
    {
        $this->filters['cod_barra'] = null;
        $this->filters['idProduto'] = null;
        $this->filters['idListaComerc'] = null;
        $this->filters['idTipoAliqIcms'] = null;
        $this->filters['idFornecedorPreco'] = null;
    }
}