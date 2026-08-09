<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/proprietario.php';

class ProprietarioDAO
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

    public  function cadastrar(Proprietario $proprietario): void
    {
        try {
            $sql = "
                INSERT INTO proprietario (id_pessoa)
                VALUES (:id_pessoa)
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_pessoa' => $proprietario->getId()
            ]);
        } catch (Exception $e) {
            error_log("proprietarioDAO::cadastrar - Error: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar proprietário: " . $e->getMessage());
        }
    }

    public  function atualizar(Proprietario $proprietario): void
    {
        try {
            $sql = "
                UPDATE proprietario
                SET id_pessoa = :id_pessoa
                WHERE id_pessoa = :id_pessoa
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_pessoa' => $proprietario->getId()
            ]);
        } catch (Exception $e) {
            error_log("proprietarioDAO::atualizar - Error: " . $e->getMessage());
            throw new Exception("Erro ao atualizar proprietário: " . $e->getMessage());
        }
    }
}
