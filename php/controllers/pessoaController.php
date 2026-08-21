<?php

require_once __DIR__ . '/../dao/pessoaDAO.php';
require_once __DIR__ . '/../dao/proprietarioDAO.php';
require_once __DIR__ . '/../dao/corretorDAO.php';
require_once __DIR__ . '/../dao/historicoDAO.php';
require_once __DIR__ . '/../dao/clienteDAO.php';
require_once __DIR__ . '/../dao/funcionarioDAO.php';
require_once __DIR__ . '/../dao/pessoaDAO.php';
require_once __DIR__ . '/../dao/enderecoDAO.php';
require_once __DIR__ . '/../services/pessoaService.php';
require_once __DIR__ . '/../model/validacao.php';
require_once __DIR__ . '/imovelController.php';

class PessoaController
{

    function montarJson(array $listaUsuarios)
    {

        if (!$listaUsuarios) {
            return (["status" => "erro", "mensagem" => "Nenhum usuário encontrado"]);
        }
        $lista = [];

        if ($listaUsuarios) {
            foreach ($listaUsuarios as $usuario) {
                if (!$usuario) {
                    continue;
                }
                $lista[] = [
                    "id" => $usuario->getId() ?? null,
                    "email" => $usuario->getEmail(),
                    "nome" => $usuario->getNome(),
                    "cpf_cnpj" => $usuario->getCpfCnpj(),
                    "rg" => $usuario->getRg(),
                    "telefones" => [$usuario->getTelefones()],
                    "endereco" => $usuario->getEndereco() ? [
                        "rua" => $usuario->getEndereco()->rua ?? null,
                        "numero" => $usuario->getEndereco()->numero ?? null,
                        "bairro" => $usuario->getEndereco()->bairro ?? null,
                        "cidade" => $usuario->getEndereco()->cidade ?? null,
                        "uf" => $usuario->getEndereco()->uf ?? null,
                        "cep" => $usuario->getEndereco()->cep ?? null,
                        "complemento" => $usuario->getEndereco()->complemento ?? null
                    ] : null,
                    "creci" => $usuario instanceof Corretor ? $usuario->getCreci() ?? null : null,
                    "salario" => method_exists($usuario, 'getSalario') ? $usuario->getSalario() ?? null : null,
                    "data_nascimento" => $usuario->getDataNascimento() ? $usuario->getDataNascimento()->format('d-m-Y') : null,


                    "tipo" => ($usuario instanceof Proprietario ? "PROPRIETARIO" : ($usuario instanceof Cliente ? "CLIENTE" : $usuario?->getCargo()->value ?? null)),
                    "data_cadastro" => $usuario->getDataCadastro() ? $usuario->getDataCadastro() : null,
                    "data_modificacao" => $usuario->getDataModificacao() ? $usuario->getDataModificacao() : null,
                    "imoveis" => array_map(function ($imovel) {
                        $controllerImovel = new ImovelController();
                        return [
                            $imovel ? $controllerImovel->montarJson([$imovel])[0] : null,
                        ];
                    }, $usuario instanceof Proprietario ? $usuario->getImoveis() ?? [] : []),
                ];
            }
        }
        return ($lista);
    }

    function listar($tipo = null)
    {
        try {
            $pessoaDAO = new PessoaDAO();
            $usuarios = $pessoaDAO->listar($tipo);
            return self::montarJson($usuarios);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao listar usuários"]);
        }
    }

    function atualizar($dados)
    {
        try {
            session_start();
            $nome = array_key_exists('nome', $dados) ? $dados['nome'] : "";
            $email = array_key_exists('email', $dados) ? $dados['email'] : "";
            $senha = array_key_exists('senha', $dados) ? $dados['senha'] : "";
            $dataNascimento = array_key_exists('data_nascimento', $dados) && Validacao::validarDataNascimento($dados['data_nascimento']) ? DateTime::createFromFormat('d/m/Y', $dados['data_nascimento']) : null;
            $cpfCnpj = array_key_exists('cpf_cnpj', $dados) ? str_replace(['.', '-', ' '], '', $dados['cpf_cnpj']) : "";
            $rg = array_key_exists('rg', $dados) && Validacao::validarRG($dados['rg']) ? $dados['rg'] : "";
            $telefones = array_key_exists('telefones', $dados) && Validacao::validarTelefone($dados['telefones']) ? str_replace(['-', '(', ')'], '', $dados['telefones']) : [];
            $tipo = array_key_exists('tipo', $dados) ? $dados['tipo'] : null;
            $pessoa = Null;
            $creci = array_key_exists('creci', $dados) && Validacao::validarCreci($dados['creci']) ? $dados['creci'] : "";
            $salario = array_key_exists('salario', $dados) && Validacao::validarSalario($dados['salario']) ? str_replace(['-', 'R$', ' '], '', $dados['salario']) : 0.0;
            $id = array_key_exists('id', $dados) ? $dados['id'] : 0;
            $rua = array_key_exists('rua', $dados) ? $dados['rua'] : "";
            $numero = array_key_exists('numero', $dados) ? $dados['numero'] : 0;
            $bairro = array_key_exists('bairro', $dados) ? $dados['bairro'] : "";
            $cidade = array_key_exists('cidade', $dados) ? $dados['cidade'] : "";
            $uf = array_key_exists('uf', $dados) ? $dados['uf'] : "";
            $cep = array_key_exists('cep', $dados) && Validacao::validarCEP($dados['cep']) ? str_replace('-', '', $dados['cep']) : "";
            $complemento = array_key_exists('complemento', $dados) ? $dados['complemento'] : "";

            if ($cpfCnpj && Validacao::validarCPF($cpfCnpj) == false) {
                error_log("CPF/CNPJ inválido: " . $cpfCnpj);
                return (["status" => "erro", "mensagem" => "CPF/CNPJ inválido"]);
            }

            if ($cpfCnpj == "") {
                error_log("CPF/CNPJ é obrigatório");
                return (["status" => "erro", "mensagem" => "CPF/CNPJ é obrigatório"]);
            }

            if ($id > 0) {
                $pessoaDAO = new PessoaDAO();
                $pessoa = $pessoaDAO->buscarPorId($id);
            } else {
                switch (strtoupper($tipo)) {
                    case "CORRETOR":
                        $pessoa = new Corretor($email, $nome, $cpfCnpj, $creci);
                        $pessoa->setSalario($salario);
                        break;
                    case "GERENTE":
                        $pessoa = new Funcionario($email, $nome, $cpfCnpj, Cargo::GERENTE);
                        $pessoa->setSalario($salario);
                        break;
                    case "CAPTADOR":
                        $pessoa = new Funcionario($email, $nome, $cpfCnpj, Cargo::CAPTADOR);
                        $pessoa->setSalario($salario);
                        break;
                    case "CLIENTE":
                        $pessoa = new Cliente($email, $nome, $cpfCnpj);
                        $pessoa->setNome($nome);
                        break;
                    case "PROPRIETARIO":
                        $pessoa = new Proprietario($email, $nome, $cpfCnpj);
                        $pessoa->setNome($nome);
                        break;
                    case "FINANCEIRO":
                        $pessoa = new Funcionario($email, $nome, $cpfCnpj, Cargo::FINANCEIRO);
                        $pessoa->setSalario($salario);
                        break;
                    case "VISTORIADOR":
                        $pessoa = new Funcionario($email, $nome, $cpfCnpj, Cargo::VISTORIADOR);
                        $pessoa->setSalario($salario);
                        break;
                    case "ADMINISTRADOR":
                        $pessoa = new Funcionario($email, $nome, $cpfCnpj, Cargo::ADMIN);
                        $pessoa->setSalario($salario);
                        break;
                    default:
                        error_log("Tipo de pessoa inválido: " . $tipo);
                        return (["status" => "erro", "mensagem" => "Tipo de usuário inválido"]);
                }
            }

            $pessoa->setNome($nome);
            $pessoa->setCpfCnpj($cpfCnpj);
            $pessoa->setDataNascimento($dataNascimento);
            $pessoa->setRg($rg);
            $pessoa->setTelefones($telefones);

            if ($senha) {
                $pessoa->setSenha($senha);
            }

            if ($cep) {
                $endereco = new Endereco(
                    $rua ?? "",
                    $bairro ?? "",
                    $cep ?? "",
                    $cidade ?? "",
                    $uf ?? "",
                );
                $endereco->setNumero($numero ?? null);
                $endereco->setComplemento($complemento ?? null);
            } else {
                $endereco = null;
            }

            $pessoa->setEndereco($endereco);
            $pessoa->setDataModificacao(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
            $pessoaService = new PessoaService();
            if ($id > 0) {
                $pessoaService->atualizar($pessoa);

                if (isset($_SESSION['usuario']) || !empty($_SESSION['usuario'])) {
                    try {
                        $historicoDAO = new HistoricoDAO();
                        $usuarioAtual = $_SESSION['usuario'] ?? null;
                        $historico = new Historico(alteracao: "Atualizou a pessoa", cliente: $pessoa, funcionario: $usuarioAtual);
                        $historicoDAO->cadastrar($historico);
                    } catch (Exception $e) {
                        error_log("Erro ao registrar histórico de destaque de imóveis: " . $e->getMessage());
                    }
                }

                return (["status" => "sucesso", "mensagem" => "Usuário atualizado com sucesso"]);
            } else {
                $pessoa->setDataCadastro(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
                error_log("Cadastrando nova pessoa: $pessoa");
                $pessoaService->cadastrar($pessoa);
                if (isset($_SESSION['usuario']) || !empty($_SESSION['usuario'])) {
                    try {
                        $historicoDAO = new HistoricoDAO();
                        $usuarioAtual = $_SESSION['usuario'] ?? null;
                        $historico = new Historico(alteracao: "Cadastrou a pessoa", cliente: $pessoa, funcionario: $usuarioAtual);
                        $historicoDAO->cadastrar($historico);
                    } catch (Exception $e) {
                        error_log("Erro ao registrar histórico de destaque de imóveis: " . $e->getMessage());
                    }
                }
                return (["status" => "sucesso", "mensagem" => "Usuário cadastrado com sucesso"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao atualizar usuário: " . $e->getMessage()]);
        }
    }

    function buscarPorId(int $id)
    {
        try {
            $pessoaDAO = new PessoaDAO();
            $usuario = $pessoaDAO->buscarPorId($id);
            if ($usuario) {
                return self::montarJson([$usuario])[0];
            } else {
                return (["status" => "erro", "mensagem" => "Usuário não encontrado"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao buscar usuário: " . $e->getMessage()]);
        }
    }

    function apagar(int $id)
    {
        try {
            session_start();
            $pessoaDAO = new PessoaDAO();
            $usuario = $pessoaDAO->buscarPorId($id);
            if ($usuario) {
                $remocao = $pessoaDAO->getConexao()->remover("id", $id, "usuario");
                if ($remocao) {
                    if (isset($_SESSION['usuario']) || !empty($_SESSION['usuario'])) {
                        try {
                            $historicoDAO = new HistoricoDAO();
                            $usuarioAtual = $_SESSION['usuario'] ?? null;
                            $historico = new Historico(alteracao: "Apagou a pessoa ", cliente: $usuario, funcionario: $usuarioAtual);
                            $historicoDAO->cadastrar($historico);
                        } catch (Exception $e) {
                            error_log("Erro ao registrar histórico de destaque de imóveis: " . $e->getMessage());
                        }
                    }
                    return (["status" => "sucesso", "mensagem" => "Usuário removido com sucesso"]);
                } else {
                    return (["status" => "erro", "mensagem" => "Erro ao remover usuário"]);
                }
            } else {
                return (["status" => "erro", "mensagem" => "Usuário não encontrado"]);
            }
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => $e->getMessage()]);
        }
    }
}
