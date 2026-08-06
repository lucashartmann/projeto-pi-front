<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/funcionario.php';

class FuncionarioDAO
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

    public function cadastrar(Funcionario $funcionario)
    {
        try {
            $sql = "
                INSERT INTO funcionario (id_pessoa, matricula, salario, data_admissao, cargo)
                VALUES (:id_pessoa, :matricula, :salario, :data_admissao, :cargo)
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_pessoa' => $funcionario->getId(),
                ':matricula' => $funcionario->getMatricula(),
                ':salario' => $funcionario->getSalario(),
                ':data_admissao' => $funcionario->getDataAdmissao() ? $funcionario->getDataAdmissao()->format('Y-m-d') : null,
                ':cargo' => $funcionario->getCargo()->value
            ]);
        } catch (Exception $e) {
            throw new Exception("Erro ao cadastrar funcionário: " . $e->getMessage());
        }
    }

    public function atualizar(Funcionario $funcionario)
    {
        try {
            $sql = "
                UPDATE funcionario
                SET matricula = :matricula,
                    salario = :salario,
                    data_admissao = :data_admissao,
                    cargo = :cargo
                WHERE id_pessoa = :id_pessoa
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_pessoa' => $funcionario->getId(),
                ':matricula' => $funcionario->getMatricula(),
                ':salario' => $funcionario->getSalario(),
                ':data_admissao' => $funcionario->getDataAdmissao() ? $funcionario->getDataAdmissao()->format('Y-m-d') : null,
                ':cargo' => $funcionario->getCargo()->value
            ]);
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar funcionário: " . $e->getMessage());
        }
    }
}