<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/funcionario.php';

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);

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

    public  function cadastrar(Funcionario $funcionario)
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
            error_log("funcionarioDAO::cadastrar - Error: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar funcionário: " . $e->getMessage());
        }
    }

    public  function atualizar(Funcionario $funcionario)
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
            error_log("funcionarioDAO::atualizar - Error: " . $e->getMessage());
            throw new Exception("Erro ao atualizar funcionário: " . $e->getMessage());
        }
    }
}
