<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/imovelDAO.php';
require_once __DIR__ . '/pessoaDAO.php';
require_once __DIR__ . '/../model/visita.php';
require_once __DIR__ . '/../model/funcionario.php';

class VisitaDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }
    public function listarPorCorretor(Funcionario $corretor): array
    {
        try {
            $lista = [];
            $visitas = $this->bancoDados->prepare("SELECT * from visita WHERE id_corretor = :id_corretor");
            if (!$visitas) {
                return $lista;
            }
            $imovelDAO = new ImovelDAO();
            $pessoaDAO = new PessoaDAO();
            $visitas->execute([':id_corretor' => $corretor]);

            foreach ($visitas as $visita) {
                $novaVisita = new Visita();
                $novaVisita->setId($visita['id_visita']);
                $novaVisita->setImovel($imovelDAO->buscarPorId($visita['id_imovel']));
                $novaVisita->setCliente($pessoaDAO->buscarPorId($visita['id_cliente']));
                $lista[] = $novaVisita;
            }

            return $lista;
        } catch (Exception $e) {
            error_log("ERRO! visitaDAO->listarPorCorretor: " . $e->getMessage());
            throw new Exception("Erro ao listar visitas por corretor: " . $e->getMessage());
        }
    }

    public function cadastrar(Visita $visita): bool
    {
        try {
            return $this->bancoDados->exec("
            INSERT INTO visita (id_cliente, id_imovel, id_corretor, data_visita, status) 
            VALUES (
                " . ($visita->getCliente() ? $visita->getCliente()->getId() : "NULL") . ",
                " . ($visita->getImovel() ? $visita->getImovel()->getId() : "NULL") . ",
                " . ($visita->getCorretor() ? $visita->getCorretor()->getId() : "NULL") . ",
                '" . ($visita->getData() ? $visita->getData()->format("Y-m-d H:i:s") : "NULL") . "',
            )
        ");
        } catch (Exception $e) {
            error_log("ERRO! visitaDAO->cadastrar: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar visita: " . $e->getMessage());
        }
    }
}
