<?php
require_once str_replace('demo', '', __DIR__) . 'vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required(['FIGURA_FISCAL_URL']);
} catch (Exception $ex) {
    die("Error when loading .env <br> msg => {$ex->getMessage()}");
}

try {
    $id = 7;
    $cnpj = "04385557000149";
    $token = "12f7feb2aa28cb315441f50c6b6900c5";
    $FiguraFiscal = new Bulla\lib\FiguraFiscal\FiguraFiscal($id, $cnpj, $token);
    // Consultar PRODUTO
    $produto = $FiguraFiscal->consultaEan("7891000100103", "Leite Condensado Moça");
    var_dump($produto);

    // Consultar NCM
    $ncm = $FiguraFiscal->consultaNCM("33051000");
    var_dump($ncm);
} catch (Exception $ex) {
    Bulla\helper\Output::print_ln($ex->getMessage(), true);
}