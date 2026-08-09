<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/corretor.php';


class CorretorDAO
{
    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function getConexao()
    {
        return $this->bancoDados;
    }


    public function cadastrar(Corretor $corretor)
    {
        try {
            $sql = "
                INSERT INTO corretor (id_funcionario, creci)
                VALUES (:id_funcionario, :creci)
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_funcionario' => $corretor->getId(),
                ':creci' => $corretor->getCreci()
            ]);
        } catch (Exception $e) {
            error_log("corretorDAO::cadastrar - Error: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar corretor: " . $e->getMessage());
        }
    }

    public function atualizar(Corretor $corretor)
    {
        try {
            $sql = "
                UPDATE corretor
                SET creci = :creci
                WHERE id_funcionario = :id_funcionario
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_funcionario' => $corretor->getId(),
                ':creci' => $corretor->getCreci()
            ]);
        } catch (Exception $e) {
            error_log("corretorDAO::atualizar - Error: " . $e->getMessage());
            throw new Exception("Erro ao atualizar corretor: " . $e->getMessage());
        }
    }
}
