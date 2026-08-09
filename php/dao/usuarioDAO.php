<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/pessoa.php';


class UsuarioDAO
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

    public  function cadastrar(Pessoa $usuario): void
    {
        try {
            $sql = "
                INSERT INTO usuario (id_pessoa, senha, ativo, ultimo_login)
                VALUES (:id_pessoa, :senha, :ativo, :ultimo_login)
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_pessoa' => $usuario->getId(),
                ':senha' => password_hash($usuario->getSenha(), PASSWORD_DEFAULT),
                ':ativo' => true,
                ':ultimo_login' => null
            ]);
        } catch (Exception $e) {
            error_log("ERRO! usuarioDAO->cadastrar: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar usuário: " . $e->getMessage());
        }
    }

    public  function atualizar(Pessoa $usuario): void
    {
        try {
            $sql = "
                UPDATE usuario
                SET senha = :senha,
                    ativo = :ativo,
                    ultimo_login = :ultimo_login
                WHERE id_pessoa = :id_pessoa
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id_pessoa' => $usuario->getId(),
                ':senha' => password_hash($usuario->getSenha(), PASSWORD_DEFAULT),
                ':ativo' => $usuario->isAtivo(),
                ':ultimo_login' => $usuario->getUltimoLogin() ? $usuario->getUltimoLogin()->format('Y-m-d H:i:s') : null
            ]);
        } catch (Exception $e) {
            error_log("ERRO! usuarioDAO->atualizar: " . $e->getMessage());
            throw new Exception("Erro ao atualizar usuário: " . $e->getMessage());
        }
    }
}
