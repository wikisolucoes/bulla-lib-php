<?php

require __DIR__ . '/vendor/autoload.php';

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