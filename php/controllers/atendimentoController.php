<?php

require_once __DIR__ . '/../dao/anuncioDAO.php';
require_once __DIR__ . '/../dao/pessoaDAO.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/notificacaoDAO.php';
require_once __DIR__ . '/../dao/atendimentoDAO.php';
require_once __DIR__ . '/pessoaController.php';
require_once __DIR__ . '/imovelController.php';

class AtendimentoController
{


    function atualizarStatus(int $idAtendimento, String $novoStatus)
    {
        try {
            $atendimentoDAO = new AtendimentoDAO();
            $atendimento = $atendimentoDAO->buscarPorId($idAtendimento);
            if (!$atendimento) {
                return (["status" => "erro", "mensagem" => "Atendimento não encontrado"]);
            }

            $atendimento->setStatus(StatusAtendimento::tryFrom($novoStatus));
            $atualizacao = $atendimentoDAO->atualizar($atendimento);

            if (!$atualizacao) {
                return (["status" => "erro", "mensagem" => "Erro ao atualizar status do atendimento"]);
            }

            return (["status" => "sucesso", "mensagem" => "Status do atendimento atualizado com sucesso"]);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao atualizar status do atendimento: " . $e->getMessage()]);
        }
    }

    function listar()
    {
        try {
            $atendimentoDAO = new AtendimentoDAO();
            $atendimentos = $atendimentoDAO->listar();
            error_log(count($atendimentos) . " atendimentos encontrados.");
            if (!$atendimentos) {
                return (["status" => "erro", "mensagem" => "Nenhum atendimento encontrado"]);
            }
            return self::montarJson($atendimentos);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar atendimentos: " . $e->getMessage()]);
        }
    }


    function cadastrar(int $idImovel)
    {
        $usuario = $_GET['usuario'] ?? null;
        $pessoaDAO = new PessoaDAO();

        if ($usuario) {
            $listaUsuarios = $pessoaDAO->listar();
            $corretores = array_filter($listaUsuarios, function ($usuario) {
                return $usuario->getCargo() === Cargo::CORRETOR;
            });
            foreach ($corretores as $corretor) {
                $notificacaoDAO = new NotificacaoDAO();
                $notificacaoDAO->cadastrar($corretor, "Cliente $usuario->getNome() quer atendimento para o imóvel de ID $idImovel", "atendimento");
            }
        }
        if (isset($_SESSION['usuario_id'])) {

            $idUsuario = $_SESSION['usuario_id'];
            $imovelDAO = new ImovelDAO();
            $atendimento = new Atendimento();
            $atendimento->setCliente($pessoaDAO->buscarPorId($idUsuario));
            $atendimento->setImovel($imovelDAO->buscarPorId($idImovel));
            $atendimento->setStatus(StatusAtendimento::PENDENTE);

            $atendimentoDAO = new AtendimentoDAO();
            $cadastro = $atendimentoDAO->cadastrar($atendimento);
            if (!$cadastro) {
                return ([
                    "status" => "erro",
                    "mensagem" => "Erro ao cadastrar atendimento"
                ]);
            } else {
                return ([
                    "status" => "sucesso",
                    "mensagem" => "Atendimento cadastrado com sucesso"
                ]);
            }
        } else {
            return ([
                "status" => "erro",
                "mensagem" => "Usuário não logado"
            ]);
        }
    }

    function montarJson(array $listaAtendimentos)
    {

        $lista = [];
        $pessoaController = new PessoaController();
        $imovelController = new ImovelController();
        foreach ($listaAtendimentos as $atendimento) {
            $lista[] = [
                "id" => $atendimento->getid(),
                "corretor" => $atendimento->getCorretor() ? $pessoaController->montarJson([$atendimento->getCorretor()])[0] : NULL,
                "cliente" => $atendimento->getCliente() ? $pessoaController->montarJson([$atendimento->getCliente()])[0] : NULL,
                "imovel" => $atendimento->getImovel() ? $imovelController->montarJson([$atendimento->getImovel()])[0] : NULL,
                "status" => $atendimento->getStatus() ? $atendimento->getStatus() : NULL,
            ];
        }
        return $lista;
    }
}
