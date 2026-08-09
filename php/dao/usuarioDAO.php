<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../dao/pessoaDAO.php';

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


    public function verificar(String $email, String $senha, bool $google = false): ?Pessoa
    {
        try {

            error_log("Verificando usuário com email: " . $email . " e senha: " . $senha . " (Google: " . ($google ? "sim" : "não") . ")");

            $stmt = $this->bancoDados->prepare("
            SELECT
                pessoa.*,
                
                usuario.id_pessoa AS usuario_id,
                usuario.senha,
                usuario.ultimo_login,
                usuario.ativo,

                corretor.id_funcionario AS corretor_id,
                corretor.creci,

                
                cliente.id_pessoa AS cliente_id,
                cliente.tipo_interesse,
                cliente.valor_minimo,
                cliente.valor_maximo,

                funcionario.id_pessoa AS funcionario_id,
                funcionario.matricula,
                funcionario.salario,
                funcionario.data_admissao,
                funcionario.cargo,

                endereco.id AS endereco_id,
                endereco.rua,
                endereco.numero,
                endereco.bairro,
                endereco.cep,
                endereco.complemento,
                endereco.cidade,
                endereco.uf
            
                FROM pessoa 
                LEFT JOIN usuario  ON usuario.id_pessoa = pessoa.id
                LEFT JOIN funcionario  ON funcionario.id_pessoa = pessoa.id
                LEFT JOIN corretor  ON corretor.id_funcionario = funcionario.id_pessoa
                LEFT JOIN cliente  ON cliente.id_pessoa = pessoa.id
                LEFT JOIN endereco  ON endereco.id = pessoa.id_endereco
                WHERE email = :email
        ");
            $stmt->execute([':email' => $email]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log(serialize($registro));

            if (!$registro) {
                error_log("Nenhum registro encontrado para o usuário com email: " . $email);
                return null;
            }

            $senha_hash_banco = "";

            if (!$google) {

                $senha_hash_banco = $registro['senha'];

                if (password_verify($senha, $senha_hash_banco) == false) {
                    error_log("Senha incorreta para o usuário com email: " . $email);
                    return null;
                }
            }

            $pessoaDAO = new PessoaDAO();
            $pessoa = $pessoaDAO->montar($registro);

            return $pessoa;
        } catch (Exception $e) {
            error_log("ERRO! pessoaDAO->verificar: " . $e->getMessage());
            throw new Exception("Erro ao verificar usuário: " . $e->getMessage());
        }
    }

    public  function buscarPorId(int $id): ?Pessoa
    {
        try {
            $sql = "
                SELECT
                pessoa.*,
                
                usuario.id_pessoa AS usuario_id,
                usuario.senha,
                usuario.ultimo_login,
                usuario.ativo,

                corretor.id_funcionario AS corretor_id,
                corretor.creci,
                
                cliente.id_pessoa AS cliente_id,
                cliente.tipo_interesse,
                cliente.valor_minimo,
                cliente.valor_maximo,

                funcionario.id_pessoa AS funcionario_id,
                funcionario.matricula,
                funcionario.salario,
                funcionario.data_admissao,
                funcionario.cargo,

                endereco.id AS endereco_id,
                endereco.rua,
                endereco.numero,
                endereco.bairro,
                endereco.cep,
                endereco.complemento,
                endereco.cidade,
                endereco.uf
            
                FROM pessoa 
                LEFT JOIN usuario  ON usuario.id_pessoa = pessoa.id
                LEFT JOIN funcionario  ON funcionario.id_pessoa = pessoa.id
                LEFT JOIN corretor  ON corretor.id_funcionario = funcionario.id_pessoa
                LEFT JOIN cliente  ON cliente.id_pessoa = pessoa.id
                LEFT JOIN endereco  ON endereco.id = pessoa.id_endereco
                LEFT JOIN proprietario  ON proprietario.id_pessoa = pessoa.id
                WHERE usuario.id_pessoa = ?
            ";

            $stmt = Banco::getInstance()->prepare($sql);
            $stmt->execute([$id]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                return null;
            }

            $pessoaDAO = new PessoaDAO();
            return $pessoaDAO->montar($registro);
        } catch (Exception $e) {
            error_log("ERRO! pessoaDAO->buscarPorId: " . $e->getMessage());
            throw new Exception("Erro ao buscar usuário por ID: " . $e->getMessage());
        }
    }
}
