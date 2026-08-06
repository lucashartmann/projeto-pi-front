<?php

require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/clienteDAO.php';
require_once __DIR__ . '/../dao/usuarioDAO.php';
require_once __DIR__ . '/../dao/corretorDAO.php';
require_once __DIR__ . '/../dao/funcionarioDAO.php';
require_once __DIR__ . '/../dao/proprietarioDAO.php';
require_once __DIR__ . '/../model/pessoa.php';


class PessoaService
{
    private PessoaDAO $pessoaDAO;
    private ClienteDAO $clienteDAO;
    private UsuarioDAO $usuarioDAO;
    private CorretorDAO $corretorDAO;
    private FuncionarioDAO $funcionarioDAO;
    private ProprietarioDAO $proprietarioDAO;
    private Banco $bancoDados;

    public function __construct()
    {
        $this->pessoaDAO = new PessoaDAO();
        $this->clienteDAO = new ClienteDAO();
        $this->usuarioDAO = new UsuarioDAO();
        $this->corretorDAO = new CorretorDAO();
        $this->funcionarioDAO = new FuncionarioDAO();
        $this->proprietarioDAO = new ProprietarioDAO();
        $this->bancoDados = Banco::getInstance();
    }

    public function cadastrar(Pessoa $pessoa): void
    {
        $this->bancoDados->beginTransaction();

        try {

            $idPessoa = $this->pessoaDAO->cadastrar($pessoa);
            $pessoa->setId($idPessoa);

            if ($pessoa->getSenha() !== null) {
                $this->usuarioDAO->cadastrar($pessoa);
            }

            if ($pessoa instanceof Corretor) {
                $this->funcionarioDAO->cadastrar($pessoa);
                $this->corretorDAO->cadastrar($pessoa);
            } elseif ($pessoa instanceof Funcionario) {
                $this->funcionarioDAO->cadastrar($pessoa);
            } elseif ($pessoa instanceof Cliente) {
                $this->clienteDAO->cadastrar($pessoa);
            } elseif ($pessoa instanceof Proprietario) {
                $this->proprietarioDAO->cadastrar($pessoa);
            }

            $this->bancoDados->commit();
        } catch (Exception $e) {

            $this->bancoDados->rollBack();
            throw $e;
        }
    }

    public function atualizar(Pessoa $pessoa)
    {
        $this->bancoDados->beginTransaction();

        try {

            $this->pessoaDAO->atualizar($pessoa);

            if ($pessoa->getSenha() !== null) {
                $this->usuarioDAO->atualizar($pessoa);
            }


            if ($pessoa instanceof Corretor) {
                $this->funcionarioDAO->atualizar($pessoa);
                $this->corretorDAO->atualizar($pessoa);
            } elseif ($pessoa instanceof Funcionario) {
                $this->funcionarioDAO->atualizar($pessoa);
            } elseif ($pessoa instanceof Cliente) {
                $this->clienteDAO->atualizar($pessoa);
            } elseif ($pessoa instanceof Proprietario) {
                $this->proprietarioDAO->atualizar($pessoa);
            }

            $this->bancoDados->commit();
        } catch (Exception $e) {

            $this->bancoDados->rollBack();
            throw $e;
        }
    }
}
