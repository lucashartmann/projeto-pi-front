<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/imovelDAO.php';
require_once __DIR__ . '/usuarioDAO.php';
require_once __DIR__ . '/../model/visita.php';

class VisitaDAO
{
    private Banco $bancoDados;
    private ImovelDAO $imovelDAO;
    private UsuarioDAO $usuarioDAO;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
        $this->imovelDAO = new ImovelDAO();
        $this->usuarioDAO = new UsuarioDAO();
    }
    public function listarPorCorretor($corretor)
    {
        $lista = [];
        $visitas = $this->bancoDados->prepare("SELECT * from visita WHERE id_corretor = :id_corretor");
        $visitas->execute([':id_corretor' => $corretor]);

        foreach ($visitas as $visita) {
            $novaVisita = new Visita();
            $novaVisita->setId($visita['id_visita']);
            $novaVisita->setImovel($this->imovelDAO->buscarPorId($visita['id_imovel']));
            $novaVisita->setCliente($this->usuarioDAO->buscarPorId($visita['id_cliente']));
            $lista[] = $novaVisita;
        }

        return $lista;
    }

    public function cadastrar($visita)
    {
        return $this->bancoDados->exec("
            INSERT INTO visita (id_cliente, id_imovel, id_corretor, data_visita, status) 
            VALUES (
                " . ($visita->getCliente() ? $visita->getCliente()->getId() : "NULL") . ",
                " . ($visita->getImovel() ? $visita->getImovel()->getId() : "NULL") . ",
                " . ($visita->getCorretor() ? $visita->getCorretor()->getId() : "NULL") . ",
                '" . ($visita->getDataVisita() ? $visita->getDataVisita()->format("Y-m-d H:i:s") : "NULL") . "',
                '" . ($visita->getStatus() ? $visita->getStatus()->value : "NULL") . "'
            )
        ");
    }

}