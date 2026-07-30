<?php

require_once __DIR__ . '/../dao/proprietarioDAO.php';
require_once __DIR__ . '/usuarioController.php';

class ProprietarioController
{

    private ProprietarioDAO $proprietarioDAO;

    private UsuarioController $usuarioController;

    public function __construct()
    {
        $this->proprietarioDAO = new ProprietarioDAO();
        $this->usuarioController = new UsuarioController();
    }
    function listar()
    {
        try {
            $proprietarios = $this->proprietarioDAO->listar();
            return $this->usuarioController->montarJson($proprietarios);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar proprietários"]);
        }
    }
}
