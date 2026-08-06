<?php

require_once __DIR__ . '/../dao/anuncioDAO.php';
require_once __DIR__ . '/../dao/pessoaDAO.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/notificacaoDAO.php';
require_once __DIR__ . '/../dao/atendimentoDAO.php';
require_once __DIR__ . '/usuarioController.php';
require_once __DIR__ . '/imovelController.php';

class AtendimentoController
{


    private AtendimentoDAO $atendimentoDAO;
    private PessoaDAO $pessoaDAO;
    private NotificacaoDAO $notificacaoDAO;
    private ImovelDAO $imovelDAO;
    private UsuarioController $usuarioController;
    private ImovelController $imovelController;

    public function __construct()
    {
        $this->atendimentoDAO = new AtendimentoDAO();
        $this->pessoaDAO = new PessoaDAO();
        $this->imovelDAO = new ImovelDAO();
        $this->usuarioController = new UsuarioController();
        $this->imovelController = new ImovelController();
        $this->notificacaoDAO = new NotificacaoDAO();
    }

    function atualizarStatus($idAtendimento, $novoStatus)
    {
        try {
            $atendimento = $this->atendimentoDAO->buscarPorId($idAtendimento);
            if (!$atendimento) {
                return (["status" => "erro", "mensagem" => "Atendimento não encontrado"]);
            }

            $atendimento->setStatus(StatusAtendimento::tryFrom($novoStatus));
            $atualizacao = $this->atendimentoDAO->atualizar($atendimento);

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
            $atendimentos = $this->atendimentoDAO->listar();
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

        if ($usuario) {
            $listaUsuarios = $this->pessoaDAO->listar();
            $corretores = array_filter($listaUsuarios, function ($usuario) {
                return $usuario->getCargo() === Cargo::CORRETOR;
            });
            foreach ($corretores as $corretor) {
                $this->notificacaoDAO->cadastrar($corretor, "Cliente $usuario->getNome() quer atendimento para o imóvel de ID $idImovel", "atendimento");
            }
        }
        if (isset($_SESSION['usuario_id'])) {

            $idUsuario = $_SESSION['usuario_id'];

            $atendimento = new Atendimento();
            $atendimento->setCliente($this->pessoaDAO->buscarPorId($idUsuario));
            $atendimento->setImovel($this->imovelDAO->buscarPorId($idImovel));
            $atendimento->setStatus(StatusAtendimento::PENDENTE);

            $cadastro = $this->atendimentoDAO->cadastrar($atendimento);
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
        foreach ($listaAtendimentos as $atendimento) {
            $lista[] = [
                "id" => $atendimento->getid(),
                "corretor" => $atendimento->getCorretor() ? $this->usuarioController->montarJson([$atendimento->getCorretor()])[0] : NULL,
                "cliente" => $atendimento->getCliente() ? $this->usuarioController->montarJson([$atendimento->getCliente()])[0] : NULL,
                "imovel" => $atendimento->getImovel() ? $this->imovelController->montarJson([$atendimento->getImovel()])[0] : NULL,
                "status" => $atendimento->getStatus() ? $atendimento->getStatus() : NULL,
            ];
        }
        return $lista;
    }
}
