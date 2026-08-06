<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/funcionario.php';
require_once __DIR__ . '/imovelDAO.php';
require_once __DIR__ . '/telefoneDAO.php';

class PessoaDAO
{
    private Banco $bancoDados;
    private ImovelDAO $imovelDAO;
    private TelefoneDAO $telefoneDAO;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
        $this->imovelDAO = new ImovelDAO();
        $this->telefoneDAO = new TelefoneDAO();
    }

    public function getConexao()
    {
        return $this->bancoDados;
    }

    public function cadastrar(Pessoa $pessoa)
    {
        try {
            $sql = "
                INSERT INTO pessoa (email, nome, cpf_cnpj, rg, id_endereco, data_nascimento, ativo)
                VALUES (:email, :nome, :cpf_cnpj, :rg, :id_endereco, :data_nascimento, :ativo)
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':email' => $pessoa->getEmail(),
                ':nome' => $pessoa->getNome(),
                ':cpf_cnpj' => $pessoa->getCpfCnpj(),
                ':rg' => $pessoa->getRg(),
                ':id_endereco' => $pessoa->getEndereco() ? $pessoa->getEndereco()->getId() : null,
                ':data_nascimento' => $pessoa->getDataNascimento() ? $pessoa->getDataNascimento()->format('Y-m-d') : null,
                ':ativo' => true
            ]);
            return (int)$this->bancoDados->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erro ao cadastrar pessoa: " . $e->getMessage());
        }
    }

    public function atualizar(Pessoa $pessoa)
    {
        try {
            $sql = "
                UPDATE pessoa
                SET email = :email,
                    nome = :nome,
                    cpf_cnpj = :cpf_cnpj,
                    rg = :rg,
                    id_endereco = :id_endereco,
                    data_nascimento = :data_nascimento,
                    ativo = :ativo,
                    data_modificacao = CURRENT_TIMESTAMP
                WHERE id = :id
            ";
            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([
                ':id' => $pessoa->getId(),
                ':email' => $pessoa->getEmail(),
                ':nome' => $pessoa->getNome(),
                ':cpf_cnpj' => $pessoa->getCpfCnpj(),
                ':rg' => $pessoa->getRg(),
                ':id_endereco' => $pessoa->getEndereco() ? $pessoa->getEndereco()->getId() : null,
                ':data_nascimento' => $pessoa->getDataNascimento() ? $pessoa->getDataNascimento()->format('Y-m-d') : null,
                ':ativo' => $pessoa->isAtivo()
            ]);
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar pessoa: " . $e->getMessage());
        }
    }

    public function montar(array $registro): ?Pessoa
    {
        try {
            $pessoa = null;

            $endereco = new Endereco($registro["rua"], $registro["bairro"], $registro["cep"], $registro["cidade"], $registro["uf"]);
            $endereco->setId($registro["id_endereco"]);
            $endereco->setComplemento($registro["complemento"]);
            $endereco->setNumero($registro["numero"]);

            if ($registro["funcionario_id"] !== null) {
                $pessoa = new Funcionario($registro["email"], $registro["nome"], $registro["cpf_cnpj"], $registro["cargo"] ? Cargo::tryFrom($registro["cargo"]) : null);
                $pessoa->setMatricula($registro["matricula"]);
                $pessoa->setSalario($registro["salario"]);
                $pessoa->setDataAdmissao($registro["data_admissao"] ? new DateTime($registro["data_admissao"]) : null);
            }

            if ($registro["cliente_id"] !== null) {
                $pessoa = new Cliente($registro["email"], $registro["nome"], $registro["cpf_cnpj"]);
            }

            if ($registro["proprietario_id"] !== null && $registro["corretor_id"] === null) {
                $pessoa = new Proprietario($registro["email"], $registro["nome"], $registro["cpf_cnpj"]);
                $pessoa->setImoveis($this->imovelDAO->listarPorProprietario($pessoa));
            }

            if ($registro["corretor_id"] !== null) {
                $pessoa = new Corretor($registro["email"], $registro["nome"], $registro["cpf_cnpj"], $registro["creci"]);
            }

            if ($pessoa === null) {
                $pessoa = new Pessoa($registro["email"], $registro["nome"], $registro["cpf_cnpj"]);
            }

            $pessoa->setId($registro["id"]);
            $pessoa->setEmail($registro["email"]);
            $pessoa->setNome($registro["nome"]);
            $pessoa->setCpfCnpj($registro["cpf_cnpj"]);
            $pessoa->setRg($registro["rg"]);
            $pessoa->setDataNascimento($registro["data_nascimento"] ? new DateTime($registro["data_nascimento"]) : null);
            $pessoa->setDataCadastro($registro["data_cadastro"] ? new DateTime($registro["data_cadastro"]) : null);
            $pessoa->setDataModificacao($registro["data_modificacao"] ? new DateTime($registro["data_modificacao"]) : null);
            $pessoa->setTelefones($this->telefoneDAO->listarPorPessoa($registro["id"]));
            $pessoa->setEndereco($endereco);
            $pessoa->setAtivo($registro["ativo"]);
            if ($registro['senha'] !== null) {
                $pessoa->setSenha($registro['senha']);
            }
            if ($registro["ativo"] !== null) {
                $pessoa->setAtivo((bool)$registro["ativo"]);
            }
            $pessoa->setUltimoLogin($registro['ultimo_login'] ? new DateTime($registro['ultimo_login']) : null);

            return $pessoa;
        } catch (Exception $e) {
            throw new Exception("Erro ao montar pessoa: " . $e->getMessage());
        }
    }

    public function buscarPorId(int $id): ?Pessoa
    {
        try {
            $sql = "
            SELECT
                pessoa.*,
                usuario.*,
                funcionario.*,
                corretor.*,
                cliente.*,
                proprietario.*,
                endereco.*,
                endereco.id AS id_endereco,
                funcionario.id_pessoa AS funcionario_id,
                corretor.id_funcionario AS corretor_id,
                cliente.id_pessoa AS cliente_id,
                proprietario.id_pessoa AS proprietario_id
            FROM pessoa 
            LEFT JOIN usuario  ON usuario.id_pessoa = pessoa.id
            LEFT JOIN funcionario  ON funcionario.id_pessoa = pessoa.id
            LEFT JOIN corretor  ON corretor.id_funcionario = funcionario.id_pessoa
            LEFT JOIN cliente  ON cliente.id_pessoa = pessoa.id
            LEFT JOIN proprietario  ON proprietario.id_pessoa = pessoa.id
            LEFT JOIN endereco  ON endereco.id = pessoa.id_endereco
            WHERE pessoa.id = ?
            ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([$id]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($registro === false) {
                return null;
            }

            return $this->montar($registro);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar usuário por ID: " . $e->getMessage());
        }
    }

    public function listar($tipo = null): array
    {
        try {
            $sql = "
            SELECT
                pessoa.*,
                usuario.*,
                funcionario.*,
                corretor.*,
                cliente.*,
                proprietario.*,
                endereco.*,
                endereco.id AS id_endereco,
                funcionario.id_pessoa AS funcionario_id,
                corretor.id_funcionario AS corretor_id,
                cliente.id_pessoa AS cliente_id,
                proprietario.id_pessoa AS proprietario_id
            FROM pessoa 
            LEFT JOIN usuario  ON usuario.id_pessoa = pessoa.id
            LEFT JOIN funcionario  ON funcionario.id_pessoa = pessoa.id
            LEFT JOIN corretor  ON corretor.id_funcionario = funcionario.id_pessoa
            LEFT JOIN cliente  ON cliente.id_pessoa = pessoa.id
            LEFT JOIN proprietario  ON proprietario.id_pessoa = pessoa.id
            LEFT JOIN endereco  ON endereco.id = pessoa.id_endereco
            ";

            if ($tipo !== null) {
                $sql .= " WHERE " . $tipo . ".id_pessoa IS NOT NULL";
            }

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();

            $pessoas = [];
            while ($registro = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pessoas[] = $this->montar($registro);
            }

            return $pessoas;
        } catch (Exception $e) {
            throw new Exception("Erro ao listar pessoas: " . $e->getMessage());
        }
    }

    public function listarPorIdImovel($idImovel): array
    {
        try {
            $sql = "
            SELECT
                p.*,
                e.*,
                e.id AS id_endereco
            FROM pessoa p
            INNER JOIN proprietario_imovel pi
                ON pi.id_proprietario = p.id
            LEFT JOIN endereco e
                ON e.id = p.id_endereco
            WHERE pi.id_imovel = ?
            ";

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute([$idImovel]);

            $pessoas = [];
            while ($registro = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pessoas[] = $this->montar($registro);
            }

            return $pessoas;
        } catch (Exception $e) {
            throw new Exception("Erro ao listar pessoas por ID do imóvel: " . $e->getMessage());
        }
    }

    public function verificar($email, $senha, bool $google = false)
    {
        try {

            $stmt = $this->bancoDados->prepare("
            SELECT
                pessoa.*,
                usuario.*,
                corretor.*,
                cliente.*,
                proprietario.*,
                endereco.*,
                endereco.id AS id_endereco,
                funcionario.id_pessoa AS funcionario_id,
                corretor.id_funcionario AS corretor_id,
                cliente.id_pessoa AS cliente_id,
                proprietario.id_pessoa AS proprietario_id
            FROM pessoa 
            LEFT JOIN usuario  ON usuario.id_pessoa = pessoa.id
            LEFT JOIN corretor  ON corretor.id_funcionario = funcionario.id_pessoa
            LEFT JOIN cliente  ON cliente.id_pessoa = pessoa.id
            LEFT JOIN proprietario  ON proprietario.id_pessoa = pessoa.id
            LEFT JOIN endereco  ON endereco.id = pessoa.id_endereco
            WHERE email = :email
        ");
            $stmt->execute([':email' => $email]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new Exception("Usuário não encontrado");
            }

            $senha_hash_banco = "";

            if (!$google) {

                $senha_hash_banco = $registro['senha'];

                $senha_hash = hash('sha256', $senha);

                if ($senha_hash_banco !== $senha_hash) {
                    throw new Exception("Senha errada!");
                }
            }

            return $this->montar($registro);
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar usuário: " . $e->getMessage());
        }
    }
}
