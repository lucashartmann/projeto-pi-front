<?php

require_once __DIR__ . '/../dao/visitaDAO.php';
require_once __DIR__ . '/../model/visita.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/pessoaDAO.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class VisitaController
{
    public function cadastrar(array $data)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $nome = isset($data['nome']) ? $data['nome'] : '';
        $data = isset($data['data']) ? $data['data'] : '';
        $hora = isset($data['hora']) ? $data['hora'] : '';
        $idImovel = isset($data['imovel']) ? $data['imovel'] : '';
        $idCliente = isset($data['cliente']) ? $data['cliente'] : '';
        $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
        $enviarEmail = isset($data['enviarEmail']) ? $data['enviarEmail'] : false;
        $visitaDAO = new VisitaDAO();
        $imovelDAO = new ImovelDAO();
        $imovel = $imovelDAO->buscarPorId($idImovel);
        $clienteDAO = new PessoaDAO();
        $cliente = $clienteDAO->buscarPorId($idCliente);
        $dataFormatada = DateTime::createFromFormat('Y-m-d H:i:s', $data . ' ' . $hora);
        $visita = new Visita($usuario, $imovel, $cliente, $dataFormatada, $nome);
        try {
            $visitaDAO->cadastrar($visita);
            return ["status" => "sucesso", "mensagem" => "Visita cadastrada com sucesso"];
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao cadastrar visita: " . $e->getMessage()];
        }
    }

    public function remover($id) {
        $visitaDAO = new VisitaDAO();
        try {
            $visitaDAO->remover($id);
            return ["status" => "sucesso", "mensagem" => "Visita removida com sucesso"];
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao remover visita: " . $e->getMessage()];
        }
    }
}
