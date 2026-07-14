<?php

require_once __DIR__ . '/../dao/proprietarioDAO.php';
require_once __DIR__ . '/../usuarioController.php';

class ProprietarioController
{

    private $proprietarioDAO;

    private $usuarioController;

    public function __construct()
    {
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