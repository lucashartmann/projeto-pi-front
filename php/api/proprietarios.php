<?php

require_once __DIR__ . '/../controllers/proprietarioController.php';
// ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);
$acao = $_GET['acao'] ?? '';
$controller = new ProprietarioController();
switch ($acao) {

    case "listar":
        $resultado = $controller->listarProprietarios();
        break;

    default:
        $resultado = (["status" => "erro", "mensagem" => "Ação inválida"]);
        break;
}
echo json_encode($resultado);
// remover '-' do cep e converter para inteiro