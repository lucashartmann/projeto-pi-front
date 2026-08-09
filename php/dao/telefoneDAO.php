<?php

class TelefoneDAO
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

    public  function listarPorPessoa(int $id): array
    {

        try {
            $stmtTel = $this->bancoDados->prepare("
                SELECT telefone.numero
                FROM telefone_pessoa 
                JOIN telefone ON telefone.id = telefone_pessoa.id_telefone
                WHERE telefone_pessoa.id_pessoa = :id
                ");
            $stmtTel->execute([':id' => $id]);

            $telefones = [];

            while ($row = $stmtTel->fetch(PDO::FETCH_ASSOC)) {
                $telefones[] = $row['numero'];
            }

            return $telefones;
        } catch (Exception $e) {
            error_log("ERRO! telefoneDAO->listarPorPessoa: " . $e->getMessage());
            throw new Exception("Erro ao listar telefones: " . $e->getMessage());
        }
    }

    public  function cadastrar(Pessoa $pessoa): void
    {
        try {
            $stmtTel = $this->bancoDados->prepare("
                INSERT INTO telefone (numero) VALUES (:numero)
                ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)
            ");

            $stmtTelPessoa = $this->bancoDados->prepare("
                INSERT IGNORE INTO telefone_pessoa (id_pessoa, id_telefone) VALUES (:id_pessoa, :id_telefone)
            ");

            foreach ($pessoa->getTelefones() as $telefone) {
                $stmtTel->execute([':numero' => $telefone]);
                $idTelefone = $this->bancoDados->lastInsertId();

                $stmtTelPessoa->execute([
                    ':id_pessoa' => $pessoa->getId(),
                    ':id_telefone' => $idTelefone
                ]);
            }
        } catch (Exception $e) {
            error_log("ERRO! telefoneDAO->cadastrar: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar telefone: " . $e->getMessage());
        }
    }
}
