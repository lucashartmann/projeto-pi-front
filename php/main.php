<?php

require_once __DIR__ . 'controller/controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';

    switch ($acao) {

        case 'cadastro_imovel':
            // lógica para cadastro
            echo "Processando currículo...";
            break;
    }
}