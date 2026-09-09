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

    public function montarJson(array $vistorias)
    {
        $lista = [];
        $pessoaController = new PessoaController();
        $imovelController = new ImovelController();
        if (!$vistorias) {
            return $lista;
        }
        foreach ($vistorias as $vistoria) {
            $lista[] = [
                "id" => $vistoria->getId(),
                "nome" => $vistoria->getNome(),
                "data" => $vistoria->getData()->format('Y-m-d H:i'),
                "imovel" => $vistoria->getImovel() ? $imovelController->montarJson([$vistoria->getImovel()])[0] : NULL,
                "vistoriador" => $vistoria->getVistoriador() ? $pessoaController->montarJson([$vistoria->getVistoriador()])[0] : NULL,
                "relatorio" => $vistoria->getRelatorio(),
            ];
        }
        return $lista;
    }

    public function listarPorVistoriador()
    {
        try {
            $vistoriaDAO = new VistoriaDAO();
            $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
            if ($usuario === null) {
                return ["status" => "erro", "mensagem" => "Usuário não autenticado"];
            }
            $vistorias = $vistoriaDAO->listarPorVistoriador($usuario);
            if (!$vistorias) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhuma vistoria encontrada"
                ];
            } else {
                $resposta = self::montarJson($vistorias);
                return $resposta;
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar vistorias: " . $e->getMessage()]);
        }
    }

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
        if ($idImovel === '') {
            return ["status" => "erro", "mensagem" => "ID do imóvel não fornecido"];
        }
        if ($usuario === null) {
            return ["status" => "erro", "mensagem" => "Usuário não autenticado"];
        }
        $dataFormatada = DateTime::createFromFormat('Y-m-d H:i', $data . ' ' . $hora);
        $imovelDAO = new ImovelDAO();
        $imovel = $imovelDAO->buscarPorId($idImovel);
        if ($imovel === null) {
            return ["status" => "erro", "mensagem" => "Imóvel não encontrado"];
        }
        $vistoria = new Vistoria($usuario, $imovel, $dataFormatada, $relatorio, $nome);
        try {
            $vistoriaDAO = new VistoriaDAO();
            $vistoriaDAO->cadastrar($vistoria);
            return ["status" => "sucesso", "mensagem" => "Vistoria cadastrada com sucesso"];
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao cadastrar vistoria: " . $e->getMessage()];
        }
    }

    public function remover($id)
    {
        $vistoriaDAO = new VistoriaDAO();
        try {
            $vistoriaDAO->remover($id);
            return ["status" => "sucesso", "mensagem" => "Vistoria removida com sucesso"];
        } catch (Exception $e) {
            return ["status" => "erro", "mensagem" => "Erro ao remover vistoria: " . $e->getMessage()];
        }
    }
}
