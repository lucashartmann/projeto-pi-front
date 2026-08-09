<?php

require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/clienteDAO.php';
require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/corretorDAO.php';
require_once __DIR__ . '/../dao/funcionarioDAO.php';
require_once __DIR__ . '/../dao/proprietarioDAO.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../database/banco.php';
require_once __DIR__ . '/../dao/enderecoDAO.php';

class PessoaService
{

    private Banco $bancoDados;

    public function __construct()
    {
        $this->bancoDados = Banco::getInstance();
    }

    public function cadastrar(Pessoa $pessoa): Pessoa
    {
        $this->bancoDados->beginTransaction();

        try {

            if ($pessoa->getEndereco() !== null) {
                $enderecoDAO = new EnderecoDAO();
                if ($enderecoDAO->verificar($pessoa->getEndereco())) {
                    $idEndereco = $enderecoDAO->verificar($pessoa->getEndereco())->getId();
                    $pessoa->getEndereco()->setId($idEndereco);
                } else {
                    $idEndereco = $enderecoDAO->cadastrar($pessoa->getEndereco());
                    $pessoa->getEndereco()->setId($idEndereco);
                }
            }

            $pessoaDAO = new PessoaDAO();
            $idPessoa = $pessoaDAO->cadastrar($pessoa);
            $pessoa->setId($idPessoa);

            if ($pessoa->getTelefones() !== null && count($pessoa->getTelefones()) > 0) {
                $telefoneDAO = new TelefoneDAO();
                $telefoneDAO->cadastrar($pessoa);
            }

            if ($pessoa->getSenha() !== null) {
                $usuarioDAO = new UsuarioDAO();
                $usuarioDAO->cadastrar($pessoa);
            }

            $funcionarioDAO = new FuncionarioDAO();

            if ($pessoa instanceof Corretor) {
                $funcionarioDAO->cadastrar($pessoa);
                $corretorDAO = new CorretorDAO();
                $corretorDAO->cadastrar($pessoa);
            } elseif ($pessoa instanceof Funcionario) {
                $funcionarioDAO->cadastrar($pessoa);
            } elseif ($pessoa instanceof Cliente) {
                $clienteDAO = new ClienteDAO();
                $clienteDAO->cadastrar($pessoa);
            } elseif ($pessoa instanceof Proprietario) {
                $proprietarioDAO = new ProprietarioDAO();
                $proprietarioDAO->cadastrar($pessoa);
            }

            $this->bancoDados->commit();

            return $pessoa;
        } catch (Exception $e) {
            error_log("ERRO PessoaService->cadastrar: " . $e->getMessage());
            $this->bancoDados->rollBack();
            throw $e;
        }
    }

    public function atualizar(Pessoa $pessoa)
    {
        $this->bancoDados->beginTransaction();

        try {
            $pessoaDAO = new PessoaDAO();
            $pessoaDAO->atualizar($pessoa);

            if ($pessoa->getSenha() !== null) {
                $usuarioDAO = new UsuarioDAO();
                $usuarioDAO->atualizar($pessoa);
            }
            $funcionarioDAO = new FuncionarioDAO();
            if ($pessoa instanceof Corretor) {
                $funcionarioDAO->atualizar($pessoa);
                $corretorDAO = new CorretorDAO();
                $corretorDAO->atualizar($pessoa);
            } elseif ($pessoa instanceof Funcionario) {
                $funcionarioDAO->atualizar($pessoa);
            } elseif ($pessoa instanceof Cliente) {
                $clienteDAO = new ClienteDAO();
                $clienteDAO->atualizar($pessoa);
            } elseif ($pessoa instanceof Proprietario) {
                $proprietarioDAO = new ProprietarioDAO();
                $proprietarioDAO->atualizar($pessoa);
            }

            $this->bancoDados->commit();
        } catch (Exception $e) {
            error_log("ERRO PessoaService->atualizar: " . $e->getMessage());
            $this->bancoDados->rollBack();
            throw $e;
        }
    }
}
