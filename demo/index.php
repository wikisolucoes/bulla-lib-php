<?php

require_once __DIR__ . '/../autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required(['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DIR_LOG']);
} catch (Exception $ex) {
    die("Error when loading .env <br> msg => {$ex->getMessage()}");
}

try {
    $CadRegrasEstaduais = new Bulla\lib\CadRegrasEstaduais();
    $CadRegrasEstaduais->setFilter('uf', 'PR');
    $CadRegrasEstaduais->setFilter('idRegimeTributario', 5);
    $CadRegrasEstaduais->setFilter('zona_franca', 0);
    $CadRegrasEstaduais->setFilter('codeNcmInicial', '1');
    $CadRegrasEstaduais->setFilter('codeNcmFinal', '99999999');
    $CadRegrasEstaduais->setFilter('idCategoria', 4);
    $regraEstadual = $CadRegrasEstaduais->get();
    Bulla\helper\Output::print_ln("Regra Tributária Estadual => {$regraEstadual->id}");

} catch (Exception $ex) {
    Bulla\helper\Output::print_ln($ex->getMessage(), true);
}

try {
    $CadRegrasFederais = new Bulla\lib\CadRegrasFederais();
    $CadRegrasFederais->setFilter('uf', 'PR');
    $CadRegrasFederais->setFilter('idRegimeTributario', 5);
    $CadRegrasFederais->setFilter('zona_franca', 0);
    $CadRegrasFederais->setFilter('codeNcmInicial', '1');
    $CadRegrasFederais->setFilter('codeNcmFinal', '99999999');
    $CadRegrasFederais->setFilter('idCategoria', 4);
    $regraFederal = $CadRegrasFederais->get();

    Bulla\helper\Output::print_ln("Regra Tributária Federal => {$regraFederal->id}");
} catch (Exception $ex) {
    Bulla\helper\Output::print_ln($ex->getMessage(), true);
}

try {
    $CadRegrasPreco = new Bulla\lib\CadRegrasPreco();
    $CadRegrasPreco->setFilter('cod_barra', '7897595902160');
    $CadRegrasPreco->setFilter('idTipoAliqIcms', 3);
    //$CadRegrasPreco->setFilter('idFornecedorPreco', 1520);
    //$CadRegrasPreco->setFilter('idListaComerc', 2);
    //$CadRegrasPreco->setFilter('idProduto', 27417);
    $regrasPreco = $CadRegrasPreco->get();

    Bulla\helper\Output::print_ln("Regras de Preço =>");
    Bulla\helper\Output::print_array($regrasPreco);
} catch (Exception $ex) {
    Bulla\helper\Output::print_ln($ex->getMessage(), true);
}