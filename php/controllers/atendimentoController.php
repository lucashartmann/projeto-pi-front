<?php

require_once __DIR__ . '/../dao/anuncioDAO.php';
require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/imovelDAO.php';
require_once __DIR__ . '/../dao/atendimentoDAO.php';
require_once __DIR__ . '/usuarioController.php';
require_once __DIR__ . '/imovelController.php';

class AtendimentoController
{   


    private AtendimentoDAO $atendimentoDAO;
    private UsuarioDAO $usuarioDAO;

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
            $this->atendimentoDAO->getConexao()->cadastrarNotificacao($usuario, "Novo atendimento cadastrado para o imóvel de ID: $idImovel", "atendimento");
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