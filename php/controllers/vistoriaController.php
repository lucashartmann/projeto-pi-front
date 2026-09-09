<?php

require_once __DIR__ . '/../dao/vistoriaDAO.php';
require_once __DIR__ . '/../model/vistoria.php';
require_once __DIR__ . '/../model/imovel.php';
require_once __DIR__ . '/../dao/imovelDAO.php';


$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

class VistoriaController
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
        $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
        $relatorio = isset($data['relatorio']) ? $data['relatorio'] : '';
        $enviarEmail = isset($data['enviarEmail']) ? $data['enviarEmail'] : false;
        $vistoriaDAO = new VistoriaDAO();
        $dataFormatada = DateTime::createFromFormat('Y-m-d H:i:s', $data . ' ' . $hora);
        $imovelDAO = new ImovelDAO();
        $imovel = $imovelDAO->buscarPorId($idImovel);
        $vistoria = new Vistoria($usuario, $imovel, $dataFormatada, $relatorio, $nome);
        try {
            $vistoriaDAO->cadastrar($vistoria);
            return ["status" => "sucesso", "mensagem" => "Vistoria cadastrada com sucesso"];
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao cadastrar vistoria: " . $e->getMessage()];
        }
    }

    public function remover($id) {
        $vistoriaDAO = new VistoriaDAO();
        try {
            $vistoriaDAO->remover($id);
            return ["status" => "sucesso", "mensagem" => "Vistoria removida com sucesso"];
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao remover vistoria: " . $e->getMessage()];
        }
    }
}
