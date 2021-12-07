<?php

require_once __DIR__ . '/../autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required(['GALAXPAY_API_ID', 'GALAXPAY_API_HASH', 'GALAXPAY_API_HOST']);
} catch (Exception $ex) {
    die("Error when loading .env <br> msg => {$ex->getMessage()}");
}

//TESTE LISTAR CLIENTES
// try {
//     $GalaxPayClientes = new Bulla\lib\GalaxPay\Clientes();
//     $clientes = $GalaxPayClientes->get([
//         'myIds' => 'pay-61943f0f530514.01805027',
//     ]);
//     var_dump($clientes);
// } catch (Exception $ex) {
//     Bulla\helper\Output::print_ln($ex->getMessage(), true);
// }

//TESTE CRIAR CLIENTE
try {
    $GalaxPayClientes = new Bulla\lib\GalaxPay\Clientes();
    $cliente = $GalaxPayClientes->create([
        "myId" => "pay-61943f0f530514.01805027",
        "name" => "",
        "document" => "81052538363",
        "emails" => [
            "teste5230email1712@galaxpay.com.br",
            "teste4990email4890@galaxpay.com.br",
        ],
        "phones" => [
            3140201512,
            31983890110,
        ],
        "Address" => [
            "zipCode" => "30411330",
            "street" => "Rua platina",
            "number" => "1330",
            "complement" => "2º andar",
            "neighborhood" => "Prado",
            "city" => "Belo Horizonte",
            "state" => "MG",
        ],
    ]);
    var_dump($cliente);
} catch (Exception $ex) {
    Bulla\helper\Output::print_ln(nl2br($ex->getMessage()), true);
}

//TESTE EDITAR CLIENTE
// try {
//     $GalaxPayClientes = new Bulla\lib\GalaxPay\Clientes();
//     $cliente = $GalaxPayClientes->edit([
//         "myId" => "pay-61943f0f530514.01805027",
//         "name" => "TESTE DO FELIPE",
//         "document" => "81052538363",
//         "emails" => [
//             "teste5230email1712@galaxpay.com.br",
//             "teste4990email4890@galaxpay.com.br",
//         ],
//         "phones" => [
//             3140201512,
//             31983890110,
//         ],
//         "Address" => [
//             "zipCode" => "30411330",
//             "street" => "Rua platina",
//             "number" => "1330",
//             "complement" => "2º andar",
//             "neighborhood" => "Prado",
//             "city" => "Belo Horizonte",
//             "state" => "MG",
//         ],
//     ]);
//     var_dump($cliente);
// } catch (Exception $ex) {
//     Bulla\helper\Output::print_ln($ex->getMessage(), true);
// }

//TESTE EXCLUIR CLIENTE
// try {
//     $GalaxPayClientes = new Bulla\lib\GalaxPay\Clientes();
//     $result = $GalaxPayClientes->delete([
//         "myId" => "pay-61943f0f530514.01805027",
//     ]);
//     var_dump($result);
// } catch (Exception $ex) {
//     Bulla\helper\Output::print_ln($ex->getMessage(), true);
// }