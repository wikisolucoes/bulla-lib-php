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