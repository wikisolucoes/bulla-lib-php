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