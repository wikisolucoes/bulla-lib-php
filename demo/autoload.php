<?php
require_once __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function ($className) {
    try {
        $className = str_ireplace('Bulla\\', '', $className);
        $className = str_ireplace('\\', DIRECTORY_SEPARATOR, $className);
        $result = false;
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . strtolower($className) . '.php')) {
            $result = require (__DIR__ . DIRECTORY_SEPARATOR . strtolower($className) . '.php');
        } else if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $className . '.php')) {
            $result = require (__DIR__ . DIRECTORY_SEPARATOR . $className . '.php');
        }
        if (!$result) {
            throw new Exception('Falha ao carregar: ' . $className);
        }
    } catch (Exception $e) {
        $msgError = "Error: {$e->getMessage()}\n";
        $msgError .= "File: {$e->getFile()} - Line: {$e->getLine()}\n";
        $msgError .= "Route: {$e->getTraceAsString()}";
        var_dump($className);
        var_dump($msgError);
    }
});