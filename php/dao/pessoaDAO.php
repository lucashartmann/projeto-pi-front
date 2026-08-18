<?php

require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../model/endereco.php';
require_once __DIR__ . '/../model/cliente.php';
require_once __DIR__ . '/../model/corretor.php';
require_once __DIR__ . '/../model/proprietario.php';
require_once __DIR__ . '/../model/funcionario.php';
require_once __DIR__ . '/proprietarioImovelDAO.php';
require_once __DIR__ . '/telefoneDAO.php';

class PessoaDAO
{
    private Banco $bancoDados;

    private $sqlConsulta =
    "
            SELECT
                pessoa.*,
                
                usuario.id_pessoa AS usuario_id,
                usuario.senha,
                usuario.ultimo_login,
                usuario.ativo,

                corretor.id_funcionario AS corretor_id,
                corretor.creci,

                proprietario.id_pessoa AS proprietario_id,
                
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
                ";


    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function getConexao()
    {
        return $this->bancoDados;
    }

    public  function cadastrar(Pessoa $pessoa): int
    {
        try {
            $sql = "
                INSERT IGNORE INTO pessoa (email, nome, cpf_cnpj, rg, id_endereco, data_nascimento)
                VALUES (:email, :nome, :cpf_cnpj, :rg, :id_endereco, :data_nascimento)
            ";
            $stmt = Banco::getInstance()->prepare($sql);
            $stmt->execute([
                ':email' => $pessoa->getEmail(),
                ':nome' => $pessoa->getNome(),
                ':cpf_cnpj' => $pessoa->getCpfCnpj(),
                ':rg' => $pessoa->getRg(),
                ':id_endereco' => $pessoa->getEndereco() ? $pessoa->getEndereco()->getId() : null,
                ':data_nascimento' => $pessoa->getDataNascimento() ? $pessoa->getDataNascimento()->format('Y-m-d') : null,
            ]);
            return (int)Banco::getInstance()->lastInsertId();
        } catch (Exception $e) {
            error_log("ERRO! pessoaDAO->cadastrar: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar pessoa: " . $e->getMessage());
        }
    }

    public  function atualizar(Pessoa $pessoa): void
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
                    data_modificacao = CURRENT_TIMESTAMP
                WHERE id = :id
            ";
            $stmt = Banco::getInstance()->prepare($sql);
            $stmt->execute([
                ':id' => $pessoa->getId(),
                ':email' => $pessoa->getEmail(),
                ':nome' => $pessoa->getNome(),
                ':cpf_cnpj' => $pessoa->getCpfCnpj(),
                ':rg' => $pessoa->getRg(),
                ':id_endereco' => $pessoa->getEndereco() ? $pessoa->getEndereco()->getId() : null,
                ':data_nascimento' => $pessoa->getDataNascimento() ? $pessoa->getDataNascimento()->format('Y-m-d') : null
            ]);
        } catch (Exception $e) {
            error_log("ERRO! pessoaDAO->atualizar: " . $e->getMessage());
            throw new Exception("Erro ao atualizar pessoa: " . $e->getMessage());
        }
    }

    public  function montar(array $registro): ?Pessoa
    {
        try {
            $pessoa = null;

            if (!isset($registro["id"]) || $registro["id"] === null) {
                error_log("PessoaDAO->montar: Registro de pessoa inválido: ID nulo");
                return null;
            }

            if (isset($registro["id_endereco"]) && $registro["id_endereco"] !== null) {
                $endereco = new Endereco(
                    $registro["rua"],
                    $registro["bairro"],
                    $registro["cep"],
                    $registro["cidade"],
                    $registro["uf"]
                );
                $endereco->setId($registro["id_endereco"]);
                $endereco->setNumero((int)$registro["numero"]);
                $endereco->setComplemento($registro["complemento"]);
            } else {
                $endereco = null;
            }

            if (isset($registro["funcionario_id"]) && $registro["funcionario_id"] !== null) {
                $pessoa = new Funcionario($registro["email"], $registro["nome"], $registro["cpf_cnpj"], $registro["cargo"] ? Cargo::tryFrom($registro["cargo"]) : null);
                $pessoa->setMatricula($registro["matricula"]);
                $pessoa->setSalario($registro["salario"]);
                $pessoa->setDataAdmissao($registro["data_admissao"] ? new DateTime($registro["data_admissao"]) : null);
            }

            if (isset($registro["cliente_id"]) && $registro["cliente_id"] !== null) {
                $pessoa = new Cliente($registro["email"], $registro["nome"], $registro["cpf_cnpj"]);
            }

            if (isset($registro["proprietario_id"]) && $registro["proprietario_id"] !== null && $registro["corretor_id"] === null) {
                $pessoa = new Proprietario($registro["email"], $registro["nome"], $registro["cpf_cnpj"]);
                $proprietarioImovelDAO = new ProprietarioImovelDAO();
                $pessoa->setImoveis($proprietarioImovelDAO->listarPorProprietario($registro["proprietario_id"]));
            }

            if (isset($registro["corretor_id"]) && $registro["corretor_id"] !== null) {
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
            $telefoneDAO = new TelefoneDAO();
            $pessoa->setTelefones($telefoneDAO->listarPorPessoa((int) $registro["id"]));
            $pessoa->setEndereco($endereco);
            $pessoa->setSenha($registro['senha']);
            $pessoa->setAtivo($registro["ativo"] !== null ? (bool)$registro["ativo"] : null);
            $pessoa->setUltimoLogin($registro['ultimo_login']  ? new DateTime($registro['ultimo_login']) : null);

            return $pessoa;
        } catch (Exception $e) {
            error_log("ERRO! pessoaDAO->montar: " . $e->getMessage());
            return null;
        }
    }

    public  function buscarPorId(int $id): ?Pessoa
    {
        try {
            $sql = $this->sqlConsulta . "WHERE pessoa.id = ?";

            $stmt = Banco::getInstance()->prepare($sql);
            $stmt->execute([$id]);

            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                return null;
            }

            return $this->montar($registro);
        } catch (Exception $e) {
            error_log("ERRO! pessoaDAO->buscarPorId: " . $e->getMessage());
            throw new Exception("Erro ao buscar usuário por ID: " . $e->getMessage());
        }
    }

    public function listar(String $tipo = null): array
    {
        try {
            $sql = $this->sqlConsulta;

            if ($tipo !== null && is_string($tipo)) {
                switch (strtoupper($tipo)) {
                    case "CORRETOR":
                    case "ADMIN":
                    case "GERENTE":
                    case "CAPTADOR":
                    case "FINANCEIRO":
                    case "VISTORIADOR":
                        $sql .= " WHERE cargo = '$tipo'";
                        break;
                    case "PROPRIETARIO":
                        $sql .= " WHERE proprietario.id_pessoa IS NOT NULL";
                        break;
                    case "CLIENTE":
                        $sql .= " WHERE cliente.id_pessoa IS NOT NULL";
                        break;
                    default:
                        break;
                }
            }

            $stmt = $this->bancoDados->prepare($sql);
            $stmt->execute();

            $pessoas = [];
            while ($registro = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pessoas[] = $this->montar($registro);
            }

            return $pessoas;
        } catch (Exception $e) {
            error_log("ERRO! pessoaDAO->listar: " . $e->getMessage());
            throw new Exception("Erro ao listar pessoas: " . $e->getMessage());
        }
    }
}
