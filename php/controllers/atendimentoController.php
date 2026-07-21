<?php

require_once __DIR__ . '/../dao/anuncioDAO.php';
require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/notificacaoDAO.php';
require_once __DIR__ . '/../dao/atendimentoDAO.php';
require_once __DIR__ . '/usuarioController.php';
require_once __DIR__ . '/imovelController.php';

class AtendimentoController
{


    private AtendimentoDAO $atendimentoDAO;
    private UsuarioDAO $usuarioDAO;
    private NotificacaoDAO $notificacaoDAO;

    private ImovelDAO $imovelDAO;

    private UsuarioController $usuarioController;

    private ImovelController $imovelController;

    public function __construct()
    {
        $this->atendimentoDAO = new AtendimentoDAO();
        $this->usuarioDAO = new UsuarioDAO();
        $this->imovelDAO = new ImovelDAO();
        $this->usuarioController = new UsuarioController();
        $this->imovelController = new ImovelController();
        $this->notificacaoDAO = new NotificacaoDAO();
    }

    function atualizarStatusAtendimento($idAtendimento, $novoStatus)
    {
        try {
            $atendimento = $this->atendimentoDAO->getAtendimentoPorId($idAtendimento);
            if (!$atendimento) {
                return (["status" => "erro", "mensagem" => "Atendimento não encontrado"]);
            }

            $atendimento->setStatus(StatusAtendimento::tryFrom($novoStatus));
            $atualizacao = $this->atendimentoDAO->atualizarAtendimento($atendimento);

            if (!$atualizacao) {
                return (["status" => "erro", "mensagem" => "Erro ao atualizar status do atendimento"]);
            }

            return (["status" => "sucesso", "mensagem" => "Status do atendimento atualizado com sucesso"]);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao atualizar status do atendimento: " . $e->getMessage()]);
        }
    }

    function listarAtendimentos()
    {
        try {
            $atendimentos = $this->atendimentoDAO->getListaAtendimentos();
            if (!$atendimentos) {
                return (["status" => "erro", "mensagem" => "Nenhum atendimento encontrado"]);
            }
            return self::montarJsonAtendimentos($atendimentos);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar atendimentos: " . $e->getMessage()]);
        }
    }


    function cadastrarAtendimento(int $idImovel)
    {
        $usuario = $_GET['usuario'] ?? null;

        if ($usuario) {
            $this->notificacaoDAO->cadastrarNotificacao($usuario, "Novo atendimento cadastrado para o imóvel de ID: $idImovel");
        }
        if (isset($_SESSION['usuario_id'])) {

            $idUsuario = $_SESSION['usuario_id'];

            $atendimento = new Atendimento();
            $atendimento->setCliente($this->usuarioDAO->getUsuarioPorId($idUsuario));
            $atendimento->setImovel($this->imovelDAO->getImovelPorId($idImovel));
            $atendimento->setStatus(StatusAtendimento::PENDENTE);

            $cadastro = $this->atendimentoDAO->cadastrarAtendimento($atendimento);
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

    function montarJsonAtendimentos(array $listaAtendimentos)
    {

        $lista = [];
        foreach ($listaAtendimentos as $atendimento) {
            $lista[] = [
                "id" => $atendimento->getid(),
                "corretor" => $atendimento->getCorretor() ? $this->usuarioController->montarJsonUsuario([$atendimento->getCorretor()])[0] : NULL,
                "cliente" => $atendimento->getCliente() ? $this->usuarioController->montarJsonUsuario([$atendimento->getCliente()])[0] : NULL,
                "imovel" => $atendimento->getImovel() ? $this->imovelController->montarJsonImoveis([$atendimento->getImovel()])[0] : NULL,
                "status" => $atendimento->getStatus() ? $atendimento->getStatus() : NULL,
            ];
        }
        return $lista;
    }
}
