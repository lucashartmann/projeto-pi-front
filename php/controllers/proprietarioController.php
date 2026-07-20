<?php

require_once __DIR__ . '/../dao/proprietarioDAO.php';
require_once __DIR__ . '/../usuarioController.php';
require_once __DIR__ . '/../model/seguranca.php';

class ProprietarioController
{

    private ProprietarioDAO $proprietarioDAO;

    private UsuarioController $usuarioController;

    public function __construct()
    {
        Seguranca::verificarAcesso();
        $this->proprietarioDAO = new ProprietarioDAO();
        $this->usuarioController = new UsuarioController();
    }
    function listarProprietarios()
    {
        try {
            $proprietarios = $this->proprietarioDAO->getListaProprietarios();
            return $this->usuarioController->montarJsonUsuario($proprietarios);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar proprietários"]);
        }
    }
}